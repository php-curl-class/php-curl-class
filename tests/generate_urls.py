from urllib.parse import urljoin, urlparse
from itertools import product
import csv
import posixpath


def resolveComponents(url):
    """
    >>> resolveComponents('http://www.example.com/foo/bar/../../baz/bux/')
    'http://www.example.com/baz/bux/'
    >>> resolveComponents('http://www.example.com/some/path/../file.ext')
    'http://www.example.com/some/file.ext'
    """

    parsed = urlparse(url)
    new_path = posixpath.normpath(parsed.path)
    if parsed.path.endswith('/'):
        # Compensate for issue1707768
        new_path += '/'
    if new_path.startswith('//'):
        new_path = new_path[1:]
    cleaned = parsed._replace(path=new_path)
    return cleaned.geturl()


first_authorities = ['http://example.com@user:pass:7152', 'https://example.com']
second_authorities = ['', 'https://www.example.org', 'http://example.com@user:pass:1111',
                      'file://example.com', 'file://']
first_paths = ['', '/', '/foobar/bazz', 'foobar/bazz/']
second_paths = ['', '/', '/foo/bar', 'foo/bar/', './foo/../bar', 'foo/./.././bar']
first_queries = ['', '?a=1', '?a=647&b=s564']
second_queries = ['', '?a=sdf', '?a=cvb&b=987']
fragments = ['', '#foo', '#bar']

with open('urls.csv', 'wt') as f:
    csvwriter = csv.writer(f, quotechar='"', quoting=csv.QUOTE_ALL)
    csvwriter.writerow(['first_url', 'second_url', 'expected'])
    counter = 1
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
                        csvwriter.writerow([first_url, second_url, resolveComponents(urljoin(first_url, second_url))])
