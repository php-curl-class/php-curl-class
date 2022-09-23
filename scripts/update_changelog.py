from datetime import datetime
from pathlib import Path


CURRENT_FILE = Path(__file__)
ROOT = CURRENT_FILE.parents[1]


def main():
    # TODO: Fetch actual semantic version.
    release_version = '9.6.2'
    release_date = datetime.today().strftime('%Y-%m-%d')
    release_title = '{} - {}'.format(release_version, release_date)

    # TODO: Fetch actual list of changes.
    release_changes = 'Some change'
    release_content = (
        '## {}\n'
        '\n'
        '{}'
    ).format(release_title, release_changes)

    changelog_path = ROOT / 'CHANGELOG.md'
    old_content = changelog_path.read_text()
    new_content = old_content.replace(
        '<!-- CHANGELOG_PLACEHOLDER -->',
        '<!-- CHANGELOG_PLACEHOLDER -->\n\n{}'.format(release_content),
    )
    print(new_content[:500])
    changelog_path.write_text(new_content)


if __name__ == '__main__':
    main()
