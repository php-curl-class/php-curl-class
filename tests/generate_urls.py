from itertools import product
from urllib.parse import urljoin
from urllib.parse import urlparse
import csv
import posixpath


def remove_dot_segments(url):
    """
    >>> remove_dot_segments('https://www.example.com/foo/bar/../../baz/bux/')
    'https://www.example.com/baz/bux/'
    >>> remove_dot_segments('https://www.example.com/some/path/../file.ext')
    'https://www.example.com/some/file.ext'
    """

    parsed = urlparse(url)
    new_path = posixpath.normpath(parsed.path)
    if parsed.path.endswith('/'):
        # Fix missing trailing slash.
        # https://bugs.python.org/issue1707768
        new_path += '/'
    if new_path.startswith('//'):
        new_path = new_path[1:]
    cleaned = parsed._replace(path=new_path)
    return cleaned.geturl()


first_authorities = [
    'http://example.com@user:pass:7152',
    'https://example.com',
]
second_authorities = [
    '',
    'https://www.example.org',
    'http://example.com@user:pass:1111',
    'file://example.com',
    'file://',
]
first_paths = [
    '',
    '/',
    '/foobar/bazz',
    'foobar/bazz/',
]
second_paths = [
    '',
    '/',
    '/foo/bar',
    'foo/bar/',
    './foo/../bar',
    'foo/./.././bar',
]
first_queries = ['', '?a=1', '?a=647&b=s564']
second_queries = ['', '?a=sdf', '?a=cvb&b=987']
fragments = ['', '#foo', '#bar']

additional_tests = [
    {
        'args': [
            'http://www.example.com/',
            '',
        ],
        'expected': 'http://www.example.com/',
    },
    {
        'args': [
            'http://www.example.com/',
            'foo',
        ],
        'expected': 'http://www.example.com/foo',
    },
    {
        'args': [
            'http://www.example.com/',
            '/foo',
        ],
        'expected': 'http://www.example.com/foo',
    },
    {
        'args': [
            'http://www.example.com/',
            '/foo/',
        ],
        'expected': 'http://www.example.com/foo/',
    },
    {
        'args': [
            'http://www.example.com/',
            '/dir/page.html',
        ],
        'expected': 'http://www.example.com/dir/page.html',
    },
    {
        'args': [
            'http://www.example.com/dir1/page2.html',
            '/dir/page.html',
        ],
        'expected': 'http://www.example.com/dir/page.html',
    },
    {
        'args': [
            'http://www.example.com/dir1/page2.html',
            'dir/page.html',
        ],
        'expected': 'http://www.example.com/dir1/dir/page.html',
    },
    {
        'args': [
            'http://www.example.com/dir1/dir3/page.html',
            '../dir/page.html',
        ],
        'expected': 'http://www.example.com/dir1/dir/page.html',
    },
]

with open('urls.csv', 'wt') as f:
    csvwriter = csv.writer(f, quotechar='"', quoting=csv.QUOTE_ALL)
    csvwriter.writerow(['first_url', 'second_url', 'expected'])
    for test in additional_tests:
        csvwriter.writerow([test['args'][0], test['args'][1], test['expected']])
    for first_domain, second_domain in product(first_authorities, second_authorities):
        for first_path, second_path in product(first_paths, second_paths):
            for first_query, second_query in product(first_queries, second_queries):
                for first_fragment, second_fragment in product(fragments, fragments):
                    if not first_path.startswith('/'):
                        first_path = '/' + first_path
                    first_url = first_domain + first_path + first_query + first_fragment
                    if second_domain and not second_path.startswith('/'):
                        second_path = '/' + second_path
                    second_url = second_domain + second_path + second_query + second_fragment
                    if first_url != second_url:
                        expected_url = remove_dot_segments(urljoin(first_url, second_url))
                        csvwriter.writerow([first_url, second_url, expected_url])
