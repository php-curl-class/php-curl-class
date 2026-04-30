"""
Wrapper around `pre-commit autoupdate' that ignores prerelease tags.

Without this wrapper, pre-commit autoupdate would bump isort to this prerelease:

    - repo: https://github.com/pycqa/isort
    -  rev: 8.0.1
    +  rev: 9.0.0a3

It monkey-patches `pre_commit.git.get_best_candidate_tag` to look at all tags in
the upstream repo, parse them through `packaging.version.Version`, and return
the highest tag that is not a prerelease. If no PEP 440 stable tag is found, the
original pre-commit behavior is preserved.

Related discussion:
    > pre-commit does not use semantic versioning or python versioning to
    determine the git tag to pick. It picks the latest tagged version on
    `master`
    > this is working as intended
    from https://github.com/pre-commit/pre-commit/issues/995
"""

import sys

from packaging.version import InvalidVersion
from packaging.version import Version
from pre_commit import git
from pre_commit.git import NO_FS_MONITOR
from pre_commit.main import main as pre_commit_main
from pre_commit.util import cmd_output

_original_get_best_candidate_tag = git.get_best_candidate_tag


def get_best_stable_candidate_tag(rev: str, git_repo: str) -> str:
    """Return the highest non-prerelease PEP 440 tag in `git_repo`.

    Tags that don't parse as PEP 440 versions are skipped. Prereleases
    (alpha, beta, rc, dev) are skipped.

    Args:
        rev: Revision selected by pre-commit -- only used by the fallback path.
            E.g. "9.0.0a3" or "v1.2.3".
        git_repo: Path to a clone of the upstream repo whose tags are scanned.
            E.g. "/tmp/tmpabc123".

    Returns:
        A tag name. If no stable PEP 440 tag is found, falls back to
        pre-commit's original `get_best_candidate_tag(rev, git_repo)`.
    """
    tags = cmd_output(
        "git",
        *NO_FS_MONITOR,
        "tag",
        "--list",
        cwd=git_repo,
    )[1].splitlines()

    best = None
    for tag in tags:
        try:
            version = Version(tag)
        except InvalidVersion:
            continue

        if version.is_prerelease:
            continue

        if best is None or version > best[0]:
            best = (version, tag)

    if best is not None:
        return best[1]

    # Fall back to pre-commit's original implementation.
    return _original_get_best_candidate_tag(rev, git_repo)


# `pre_commit.commands.autoupdate` calls `git.get_best_candidate_tag(...)` via
# attribute access on the imported `git` module, so patching the attribute
# here takes effect at call time.
git.get_best_candidate_tag = get_best_stable_candidate_tag


if __name__ == "__main__":
    sys.exit(pre_commit_main(sys.argv[1:]))
