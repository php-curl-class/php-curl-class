import json
import os
import pprint
import subprocess
from copy import copy
from datetime import datetime
from datetime import timezone
from pathlib import Path

from git.repo import Repo
from github import Github

# The owner and repository name. For example, octocat/Hello-World.
GITHUB_REPOSITORY = os.getenv("GITHUB_REPOSITORY", "")

GITHUB_TOKEN = os.getenv("GITHUB_TOKEN")
GITHUB_REF_NAME = os.getenv("GITHUB_REF_NAME")

CURRENT_FILE = Path(__file__)
ROOT = CURRENT_FILE.parents[1]
CHANGELOG_PATH = ROOT / "CHANGELOG.md"
LIBRARY_FILE_PATH = ROOT / "src/Curl/Curl.php"

# TODO: Adjust number of recent pull requests to include likely number of
# pull requests since the last release.
RECENT_PULL_REQUEST_LIMIT = 10


def main():
    # Find most recent tag and timestamp.
    #   git for-each-ref --format="%(refname:short) | %(creatordate)" "refs/tags/*"
    local_repo = Repo(ROOT)

    # Sort the tags by version.
    #   git tag --list | sort --reverse --version-sort
    tags = sorted(
        local_repo.tags,
        key=lambda tag: list(map(int, tag.name.split("."))),
        reverse=True,
    )

    most_recent_tag = tags[0]
    print("most_recent_tag: {}".format(most_recent_tag))
    most_recent_tag_datetime = most_recent_tag.commit.committed_datetime
    print("most_recent_tag_datetime: {}".format(most_recent_tag_datetime))

    # Find merged pull requests since the most recent tag.
    github_repo = Github(login_or_token=GITHUB_TOKEN).get_repo(GITHUB_REPOSITORY)
    recent_pulls = github_repo.get_pulls(
        state="closed",
        sort="updated",
        direction="desc",
    )[:RECENT_PULL_REQUEST_LIMIT]

    pull_request_changes = []

    # Group pull requests by semantic version change type.
    pull_request_by_type = {
        "major": [],
        "minor": [],
        "patch": [],
        "cleanup": [],
        "unspecified": [],
    }

    # Track if any pull request is missing a semantic version change type.
    pulls_missing_semver_label = []

    for pull in recent_pulls:
        # print('-' * 10)

        if not pull.merged:
            # print('skipping since not merged: {}'.format(pull.title))
            # print(pull.html_url)
            continue

        # Make merged_at timestamp offset-aware. Without this, the following
        # error will appear:
        #   TypeError: can't compare offset-naive and offset-aware datetimes
        pull_merged_at = copy(pull.merged_at).replace(tzinfo=timezone.utc)

        if pull_merged_at < most_recent_tag_datetime:
            # print('skipping since merged prior to last release: {}'.format(pull.title))
            # print(pull.html_url)
            continue

        pull_labels = {label.name for label in pull.labels}
        if "major-incompatible-changes" in pull_labels:
            group_name = "major"
        elif "minor-backwards-compatible-added-functionality" in pull_labels:
            group_name = "minor"
        elif "patch-backwards-compatible-bug-fixes" in pull_labels:
            group_name = "patch"
        elif "cleanup-no-release-required" in pull_labels:
            group_name = "cleanup"
        else:
            group_name = "unspecified"
            pulls_missing_semver_label.append(pull)
        pull_request_by_type[group_name].append(pull)

        # pprint.pprint(pull.title)
        # pprint.pprint('most recent: {}'.format(most_recent_tag_datetime))
        # pprint.pprint('merged at:   {}'.format(pull.merged_at))
        # print(pull.html_url)

        if group_name in ["major", "minor", "patch"]:
            pull_request_changes.append(
                "- {} ([#{}]({}))".format(pull.title, pull.number, pull.html_url)
            )

        # print('-' * 10)

    # pprint.pprint(pull_request_changes)

    # Raise error if any pull request is missing a semantic version change type.
    # Do this check before checking for any pull request changes as all the pull
    # requests changes might be missing semver labels.
    if pulls_missing_semver_label:
        error_message = (
            "Merged pull request(s) found without semantic version label:\n"
            "{}".format(
                "\n".join(
                    "  {}".format(pull.html_url) for pull in pulls_missing_semver_label
                )
            )
        )
        raise Exception(error_message)

    if not pull_request_changes:
        print("No merged pull requests since the most recent tag release were found")
        return

    # pprint.pprint(pull_request_by_type)
    if pull_request_by_type.get("major"):
        highest_semantic_version = "major"
        php_file_path = "scripts/bump_major_version.php"
    elif pull_request_by_type.get("minor"):
        highest_semantic_version = "minor"
        php_file_path = "scripts/bump_minor_version.php"
    elif pull_request_by_type.get("patch"):
        highest_semantic_version = "patch"
        php_file_path = "scripts/bump_patch_version.php"
    else:
        highest_semantic_version = None
        php_file_path = ""
    print("highest_semantic_version: {}".format(highest_semantic_version))

    # Bump version and get next semantic version.
    command = ["php", php_file_path]
    print("running command: {}".format(command))
    proc = subprocess.Popen(
        command, shell=False, stdout=subprocess.PIPE, stdin=subprocess.PIPE
    )
    stdout, stderr = proc.communicate()
    print("stdout: {}".format(stdout))
    print("stderr: {}".format(stderr))
    result = json.loads(stdout)
    pprint.pprint(result)

    release_version = result["new_version"]
    today = datetime.today()
    print("today: {} (tzinfo={})".format(today, today.tzinfo))
    today = today.replace(tzinfo=timezone.utc)
    print("today: {} (tzinfo={})".format(today, today.tzinfo))
    release_date = today.strftime("%Y-%m-%d")
    print("release_date: {}".format(release_date))
    release_title = "{} - {}".format(release_version, release_date)
    print("release_title: {}".format(release_title))

    release_content = "".join(
        [
            "## {}\n",
            "\n",
            "{}",
        ]
    ).format(release_title, "\n".join(pull_request_changes))

    old_content = CHANGELOG_PATH.read_text()
    new_content = old_content.replace(
        "<!-- CHANGELOG_PLACEHOLDER -->",
        "<!-- CHANGELOG_PLACEHOLDER -->\n\n{}".format(release_content),
    )
    # print(new_content[:800])
    CHANGELOG_PATH.write_text(new_content)

    # print('git status before adding files:')
    # print(local_repo.git.status())

    local_repo.git.add(CHANGELOG_PATH)
    local_repo.git.add(LIBRARY_FILE_PATH)

    # print('git status after adding files:')
    # print(local_repo.git.status())

    # print('diff:')
    # git diff --cached --color=always
    # print(local_repo.git.diff(cached=True, color='always'))

    local_repo.git.commit(
        message=result["message"],
        author="{} <{}>".format(
            local_repo.git.config("--get", "user.name"),
            local_repo.git.config("--get", "user.email"),
        ),
    )

    print("diff after commit:")
    # git log --max-count=1 --patch --color=always
    print(local_repo.git.log(max_count="1", patch=True, color="always"))

    # Push local changes.
    server = "https://{}@github.com/{}.git".format(GITHUB_TOKEN, GITHUB_REPOSITORY)
    print(
        'pushing changes to branch "{}" of repository "{}"'.format(
            GITHUB_REF_NAME, GITHUB_REPOSITORY
        )
    )
    local_repo.git.push(server, GITHUB_REF_NAME)

    # Create tag and release.
    tag = result["new_version"]
    tag_message = result["message"]
    release_name = "Release {}".format(release_version)
    release_message = (
        "See [change log]"
        "(https://github.com/php-curl-class/php-curl-class/blob/master/CHANGELOG.md) for changes.\n"
        "\n"
        "https://github.com/php-curl-class/php-curl-class/compare/{}...{}".format(
            result["old_version"],
            result["new_version"],
        )
    )
    commit_sha = local_repo.head.commit.hexsha
    print("tag: {}".format(tag))
    print('tag_message: "{}"'.format(tag_message))
    print('release_name: "{}"'.format(release_name))
    print('release_message: "{}"'.format(release_message))
    print("commit_sha: {}".format(commit_sha))

    github_repo.create_git_tag_and_release(
        tag=tag,
        tag_message=tag_message,
        release_name=release_name,
        release_message=release_message,
        object=commit_sha,
        type="commit",
        draft=False,
    )
    print("created tag and release")


if __name__ == "__main__":
    main()
