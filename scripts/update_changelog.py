import json
import os
import pprint
import subprocess
from copy import copy
from datetime import datetime, timezone
from pathlib import Path

import git
from github import Github


# The owner and repository name. For example, octocat/Hello-World.
GITHUB_REPOSITORY = os.getenv('GITHUB_REPOSITORY')

GITHUB_TOKEN = os.getenv('GITHUB_TOKEN')
PRODUCTION = os.getenv('PRODUCTION', False)

CURRENT_FILE = Path(__file__)
ROOT = CURRENT_FILE.parents[1]
CHANGELOG_PATH = ROOT / 'CHANGELOG.md'

# TODO: Adjust number of recent pull requests to include likely number of
# pull requests since the last release.
RECENT_PULL_REQUEST_LIMIT = 10


def main():
    # Find most recent tag and timestamp.
    #   git for-each-ref --format="%(refname:short) | %(creatordate)" "refs/tags/*"
    local_repo = git.Repo(ROOT)

    # Fetch tags since `git fetch' is run with --no-tags during actions/checkout.
    #   git fetch --tags
    for remote in local_repo.remotes:
        remote.fetch('--tags')

    # Sort the tags by version.
    #   git tag --list | sort --reverse --version-sort
    tags = sorted(
        local_repo.tags,
        key=lambda tag: list(map(int, tag.name.split('.'))), reverse=True)

    most_recent_tag = tags[0]
    print('most_recent_tag: {}'.format(most_recent_tag))
    most_recent_tag_datetime = most_recent_tag.commit.committed_datetime
    print('most_recent_tag_datetime: {}'.format(most_recent_tag_datetime))

    # Find merged pull requests since the most recent tag.
    github_repo = Github(login_or_token=GITHUB_TOKEN).get_repo(GITHUB_REPOSITORY)
    recent_pulls = github_repo.get_pulls(
        state='closed',
        sort='updated',
        direction='desc',
    )[:RECENT_PULL_REQUEST_LIMIT]

    pull_request_changes = []

    # Group pull requests by semantic version change type.
    pull_request_by_type = {
        'major': [],
        'minor': [],
        'patch': [],
        'unspecified': [],
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

        pull_labels = {
            label.name for label in pull.labels
        }
        if 'major-incompatible-changes' in pull_labels:
            group_name = 'major'
        elif 'minor-backwards-compatible-added-functionality' in pull_labels:
            group_name = 'minor'
        elif 'patch-backwards-compatible-bug-fixes' in pull_labels:
            group_name = 'patch'
        else:
            group_name = 'unspecified'
            pulls_missing_semver_label.append(pull)
        pull_request_by_type[group_name].append(pull)

        # pprint.pprint(pull.title)
        # pprint.pprint('most recent: {}'.format(most_recent_tag_datetime))
        # pprint.pprint('merged at:   {}'.format(pull.merged_at))
        # print(pull.html_url)

        pull_request_changes.append(
            '- {} ([#{}]({}))'.format(pull.title, pull.number, pull.html_url)
        )

        # print('-' * 10)

    # pprint.pprint(pull_request_changes)

    # pprint.pprint(pull_request_by_type)
    highest_semantic_version = None
    php_file_path = ''
    if pull_request_by_type.get('major'):
        highest_semantic_version = 'major'
        php_file_path = './bump_major_version.php'
    elif pull_request_by_type.get('minor'):
        highest_semantic_version = 'minor'
        php_file_path = './bump_minor_version.php'
    elif pull_request_by_type.get('patch'):
        highest_semantic_version = 'patch'
        php_file_path = './bump_patch_version.php'
    print('highest_semantic_version: {}'.format(highest_semantic_version))

    # Bump version and get next semantic version.
    command = ['php', php_file_path]
    print('running command: {}'.format(command))
    proc = subprocess.Popen(command, shell=False, stdout=subprocess.PIPE, stdin=subprocess.PIPE)
    stdout, stderr = proc.communicate()
    result = json.loads(stdout)
    pprint.pprint(result)

    release_version = result['new_version']
    release_date = datetime.today().strftime('%Y-%m-%d')
    release_title = '{} - {}'.format(release_version, release_date)
    print('release_title: {}'.format(release_title))

    release_content = (
        '## {}\n'
        '\n'
        '{}'
    ).format(release_title, '\n'.join(pull_request_changes))

    old_content = CHANGELOG_PATH.read_text()
    new_content = old_content.replace(
        '<!-- CHANGELOG_PLACEHOLDER -->',
        '<!-- CHANGELOG_PLACEHOLDER -->\n\n{}'.format(release_content),
    )
    print(new_content[:800])
    # CHANGELOG_PATH.write_text(new_content)

    # Raise error if any pull request is missing a semantic version change type.
    if pulls_missing_semver_label:
        error_message = (
            'Merged pull request(s) found without semantic version label:\n'
            '{}'.format('\n'.join(
                '  {}'.format(pull.html_url)
                for pull in pulls_missing_semver_label)))
        raise Exception(error_message)

if __name__ == '__main__':
    main()
