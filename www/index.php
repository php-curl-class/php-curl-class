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

.pl-c {
    color: #969896;
}

.pl-c1, .pl-s .pl-v {
    color: #0086b3;
}

.pl-e, .pl-en {
    color: #795da3;
}

.pl-smi, .pl-s .pl-s1 {
    color: #333;
}

.pl-ent {
    color: #63a35c;
}

.pl-k {
    color: #a71d5d;
}

.pl-s, .pl-pds, .pl-s .pl-pse .pl-s1, .pl-sr, .pl-sr .pl-cce, .pl-sr .pl-sre, .pl-sr .pl-sra {
    color: #183691;
}

.pl-v {
    color: #ed6a43;
}

.pl-id {
    color: #b52a1d;
}

.pl-ii {
    background-color: #b52a1d;
    color: #f8f8f8;
}

.pl-sr .pl-cce {
    color: #63a35c;
    font-weight: bold;
}

.pl-ml {
    color: #693a17;
}

.pl-mh, .pl-mh .pl-en, .pl-ms {
    color: #1d3e81;
    font-weight: bold;
}

.pl-mq {
    color: #008080;
}

.pl-mi {
    color: #333;
    font-style: italic;
}

.pl-mb {
    color: #333;
    font-weight: bold;
}

.pl-md {
    background-color: #ffecec;
    color: #bd2c00;
}

.pl-mi1 {
    background-color: #eaffea;
    color: #55a532;
}

.pl-mdr {
    color: #795da3;
    font-weight: bold;
}

.pl-mo {
    color: #1d3e81;
}
</style>
</head>
<body>

<h1>PHP Curl Class</h1>
<h2>Easily send HTTP requests and integrate with web APIs</h2>

<figure>
    <code>
        <span class="pl-s1"><span class="pl-c1">$</span>curl</span> = <span class="pl-k">new</span> <span class="pl-v">Curl</span>();<br />
        <span class="pl-s1"><span class="pl-c1">$</span>curl</span>-&gt;<span class="pl-en">get</span>(<span class="pl-s">'https://www.example.com/'</span>);<br />
        <br />
        <span class="pl-k">if</span> (<span class="pl-s1"><span class="pl-c1">$</span>curl</span>-&gt;<span class="pl-c1">error</span>) {<br />
        &nbsp;&nbsp;&nbsp;&nbsp;<span class="pl-k">echo</span> <span class="pl-s">'Error: '</span> . <span class="pl-s1"><span class="pl-c1">$</span>curl</span>-&gt;<span class="pl-c1">errorCode</span> . <span class="pl-s">': '</span> . <span class="pl-s1"><span class="pl-c1">$</span>curl</span>-&gt;<span class="pl-c1">errorMessage</span> . <span class="pl-s">"\n"</span>;<br />
        } <span class="pl-k">else</span> {<br />
        &nbsp;&nbsp;&nbsp;&nbsp;<span class="pl-k">echo</span> <span class="pl-s">'Success! Here is the response:'</span> . <span class="pl-s">"\n"</span>;<br />
        &nbsp;&nbsp;&nbsp;&nbsp;<span class="pl-en">var_dump</span>(<span class="pl-s1"><span class="pl-c1">$</span>curl</span>-&gt;<span class="pl-c1">response</span>);<br />
        }<br />
    </code>
</figure>

<p>
    <a href="https://github.com/php-curl-class/php-curl-class/releases/">
        <img
            alt=""
            src="https://img.shields.io/github/release/php-curl-class/php-curl-class.svg?style=flat-square" />
    </a>
    <a href="https://github.com/php-curl-class/php-curl-class/blob/master/LICENSE">
        <img
            alt=""
            src="https://img.shields.io/github/license/php-curl-class/php-curl-class.svg?style=flat-square" />
    </a>
    <a href="https://github.com/php-curl-class/php-curl-class/actions/workflows/ci.yml">
        <img
            alt=""
            src="https://img.shields.io/github/workflow/status/php-curl-class/php-curl-class/ci?style=flat-square" />
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
