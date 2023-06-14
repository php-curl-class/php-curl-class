<!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title>PHP Curl Class</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="description" content="Easily send HTTP requests and integrate with web APIs using PHP Curl Class" />
<style>

body {
    color: #333;
    font-size: 16px;
    line-height: 1.6;
    margin: 40px auto;
    padding: 0 10px;
}

a {
    color: #333;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

h1 {
    font-size: 4em;
    letter-spacing: -1px;
    line-height: 1;
    margin: 36px 0 18px;
    text-align: center;
}

h2 {
    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    font-size: 1.5em;
    font-weight: 200;
    text-align: center;
}

figure {
    text-align: center;
}

code {
    background-color: #f6f8fa;
    color: #b3b3b3;
    display: inline-block;
    font-family: Monaco, Menlo, Consolas, "Courier New", monospace;
    margin: 0 auto;
    padding: 14px 20px;
    text-align: left;
}

code span {
    color: #24292e;
}

p {
    margin-left: auto;
    margin-right: auto;
    max-width: 640px;
    text-align: center;
}

p:last-child {
    font-size: 200%;
}
</style>

<style>
/* PrismJS 1.29.0
https://prismjs.com/download.html#themes=prism-solarizedlight&languages=markup+markup-templating+php */
/*
 Solarized Color Schemes originally by Ethan Schoonover
 http://ethanschoonover.com/solarized

 Ported for PrismJS by Hector Matos
 Website: https://krakendev.io
 Twitter Handle: https://twitter.com/allonsykraken)
*/

/*
SOLARIZED HEX
--------- -------
base03    #002b36
base02    #073642
base01    #586e75
base00    #657b83
base0     #839496
base1     #93a1a1
base2     #eee8d5
base3     #fdf6e3
yellow    #b58900
orange    #cb4b16
red       #dc322f
magenta   #d33682
violet    #6c71c4
blue      #268bd2
cyan      #2aa198
green     #859900
*/

code[class*="language-"],
pre[class*="language-"] {
	color: #657b83; /* base00 */
	font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace;
	font-size: 1em;
	text-align: left;
	white-space: pre;
	word-spacing: normal;
	word-break: normal;
	word-wrap: normal;

	line-height: 1.5;

	-moz-tab-size: 4;
	-o-tab-size: 4;
	tab-size: 4;

	-webkit-hyphens: none;
	-moz-hyphens: none;
	-ms-hyphens: none;
	hyphens: none;
}

pre[class*="language-"]::-moz-selection, pre[class*="language-"] ::-moz-selection,
code[class*="language-"]::-moz-selection, code[class*="language-"] ::-moz-selection {
	background: #073642; /* base02 */
}

pre[class*="language-"]::selection, pre[class*="language-"] ::selection,
code[class*="language-"]::selection, code[class*="language-"] ::selection {
	background: #073642; /* base02 */
}

/* Code blocks */
pre[class*="language-"] {
	padding: 1em;
	margin: .5em 0;
	overflow: auto;
	border-radius: 0.3em;
}

:not(pre) > code[class*="language-"],
pre[class*="language-"] {
	background-color: #fdf6e3; /* base3 */
}

/* Inline code */
:not(pre) > code[class*="language-"] {
	padding: .1em;
	border-radius: .3em;
}

.token.comment,
.token.prolog,
.token.doctype,
.token.cdata {
	color: #93a1a1; /* base1 */
}

.token.punctuation {
	color: #586e75; /* base01 */
}

.token.namespace {
	opacity: .7;
}

.token.property,
.token.tag,
.token.boolean,
.token.number,
.token.constant,
.token.symbol,
.token.deleted {
	color: #268bd2; /* blue */
}

.token.selector,
.token.attr-name,
.token.string,
.token.char,
.token.builtin,
.token.url,
.token.inserted {
	color: #2aa198; /* cyan */
}

.token.entity {
	color: #657b83; /* base00 */
	background: #eee8d5; /* base2 */
}

.token.atrule,
.token.attr-value,
.token.keyword {
	color: #859900; /* green */
}

.token.function,
.token.class-name {
	color: #b58900; /* yellow */
}

.token.regex,
.token.important,
.token.variable {
	color: #cb4b16; /* orange */
}

.token.important,
.token.bold {
	font-weight: bold;
}
.token.italic {
	font-style: italic;
}

.token.entity {
	cursor: help;
}


</style>

</head>
<body>

<h1>PHP Curl Class</h1>
<h2>Easily send HTTP requests and integrate with web APIs</h2>

<figure>
    <pre>
<code class="language-php"><!--
--><span class="token variable">$curl</span> <span class="token operator">=</span> <span class="token keyword">new</span> <span class="token class-name">Curl</span><span class="token punctuation">(</span><span class="token punctuation">)</span><span class="token punctuation">;</span>
<span class="token variable">$curl</span><span class="token operator">-></span><span class="token function">get</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'https://www.example.com/'</span><span class="token punctuation">)</span><span class="token punctuation">;</span>

<span class="token keyword">if</span> <span class="token punctuation">(</span><span class="token variable">$curl</span><span class="token operator">-></span><span class="token property">error</span><span class="token punctuation">)</span> <span class="token punctuation">{</span>
    <span class="token keyword">echo</span> <span class="token string single-quoted-string">'Error: '</span> <span class="token operator">.</span> <span class="token variable">$curl</span><span class="token operator">-></span><span class="token property">errorMessage</span> <span class="token operator">.</span> <span class="token string double-quoted-string">"\n"</span><span class="token punctuation">;</span>
    <span class="token variable">$curl</span><span class="token operator">-></span><span class="token function">diagnose</span><span class="token punctuation">(</span><span class="token punctuation">)</span><span class="token punctuation">;</span>
<span class="token punctuation">}</span> <span class="token keyword">else</span> <span class="token punctuation">{</span>
    <span class="token keyword">echo</span> <span class="token string single-quoted-string">'Success! Here is the response:'</span> <span class="token operator">.</span> <span class="token string double-quoted-string">"\n"</span><span class="token punctuation">;</span>
    <span class="token function">var_dump</span><span class="token punctuation">(</span><span class="token variable">$curl</span><span class="token operator">-></span><span class="token property">response</span><span class="token punctuation">)</span><span class="token punctuation">;</span>
<span class="token punctuation">}</span>
</code>
    </pre>
</figure>

<p>
    <a href="https://github.com/php-curl-class/php-curl-class">
        https://github.com/php-curl-class/php-curl-class
    </a>
</p>

<p>
    <a href="https://github.com/php-curl-class/php-curl-class/releases/">
        <img
            alt=""
            src="https://img.shields.io/github/release/php-curl-class/php-curl-class.svg?style=flat-square&sort=semver" />
    </a>
    <a href="https://github.com/php-curl-class/php-curl-class/blob/master/LICENSE">
        <img
            alt=""
            src="https://img.shields.io/github/license/php-curl-class/php-curl-class.svg?style=flat-square" />
    </a>
    <a href="https://github.com/php-curl-class/php-curl-class/actions/workflows/ci.yml">
        <img
            alt=""
            src="https://img.shields.io/github/actions/workflow/status/php-curl-class/php-curl-class/ci.yml?style=flat-square&label=build&branch=master" />
    </a>
    <a href="https://github.com/php-curl-class/php-curl-class/releases/">
        <img
            alt=""
            src="https://img.shields.io/github/actions/workflow/status/php-curl-class/php-curl-class/release.yml?style=flat-square&label=release&branch=master" />
    </a>
    <a href="https://github.com/php-curl-class/php-curl-class/releases/">
        <img
            alt=""
            src="https://img.shields.io/packagist/dt/php-curl-class/php-curl-class.svg?style=flat-square" />
    </a>
</p>

<p>
    <a
        href="https://www.buymeacoffee.com/zachborboa"
        title="Buy me a coffee!">☕</a>
</p>

</body>
</html>
