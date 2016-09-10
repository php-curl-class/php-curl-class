<!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title>PHP Curl Class</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="description" content="PHP Curl Class: HTTP requests made easy" />
<style>

body {
    color: #444;
    font-size: 16px;
    line-height: 1.6;
    margin: 40px auto;
    max-width: 640px;
    padding: 0 10px;
}

a {
    color: #333;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

ul {
    font-size: 18px;
    margin-left: 12px;
    margin-top: 42px;
}


code {
    background-color: #f7f7f7;
    color: #b3b3b3;
    display: block;
    font-family: monospace;
    padding: 8px 12px;
}

code span {
    color: #333;
}

ul {
    list-style-type: none;
    padding: 0;
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

<h1>PHP Curl Class: HTTP requests made easy</h1>

<code>
$ composer require php-curl-class/php-curl-class &amp;&gt; /dev/null<br />
$ php --interactive<br />
php &gt; <span class="pl-s1"><span class="pl-k">require</span> <span class="pl-c1">__DIR__</span> <span
class="pl-k">.</span> <span class="pl-s"><span class="pl-pds">'</span>/vendor/autoload.php<span
class="pl-pds">'</span></span>;</span><br />
php &gt; <span class="pl-s1"><span class="pl-k">use</span> <span class="pl-c1">\Curl\Curl</span>;</span><br />
php &gt; <span class="pl-s1"><span class="pl-smi">$curl</span> <span class="pl-k">=</span> <span
class="pl-k">new</span> <span class="pl-c1">\Curl\</span><span class="pl-c1">Curl</span>();</span><br />
php &gt; <span class="pl-s1"><span class="pl-smi">$curl</span><span
class="pl-k">-&gt;</span>setBasicAuthentication(<span class="pl-s"><span class="pl-pds">'</span>user<span
class="pl-pds">'</span></span>, <span class="pl-s"><span class="pl-pds">'</span>pass<span
class="pl-pds">'</span></span>);</span><br />
php &gt; <span class="pl-s1"><span class="pl-smi">$curl</span><span class="pl-k">-&gt;</span>get(<span
class="pl-s"><span class="pl-pds">'</span>https://api.github.com/user<span class="pl-pds">'</span></span>);</span><br />
php &gt; <span class="pl-s1"><span class="pl-c1">echo</span> <span class="pl-smi">$curl</span><span
class="pl-k">-&gt;</span><span class="pl-smi">httpStatusCode</span>;</span><br />
<span class="pl-s1"><span class="pl-c1">200</span></span><br />
php &gt; <span class="pl-s1"><span class="pl-c1">echo</span> <span class="pl-smi">$curl</span><span
class="pl-k">-&gt;</span><span class="pl-smi">responseHeaders</span>[<span class="pl-s"><span
class="pl-pds">'</span>content-type<span class="pl-pds">'</span></span>];</span><br />
<span class="pl-s1"><span class="pl-s"><span class="pl-pds"></span>application/json; charset=utf-8<span
class="pl-pds"></span></span></span><br />
php &gt; <span class="pl-s1"><span class="pl-c1">echo</span> <span class="pl-smi">$curl</span><span
class="pl-k">-&gt;</span><span class="pl-smi">response</span><span class="pl-k">-&gt;</span><span
class="pl-smi">login</span>;</span><br />
<span class="pl-s1"><span class="pl-s"><span class="pl-pds"></span>php-curl-class<span
class="pl-pds"></span></span></span><br />
php &gt; <span class="pl-s1"><span class="pl-c1">echo</span> <span class="pl-smi">$curl</span><span
class="pl-k">-&gt;</span><span class="pl-smi">rawResponse</span>;</span><br />
<span class="pl-s1"><span class="pl-s"><span
class="pl-pds"></span>{"login":"php-curl-class","id":7654321,"avatar_url": ...}<span
class="pl-pds"></span></span></span><br />
</code>

<ul>
    <li>
        <a href="https://github.com/php-curl-class/php-curl-class">
            https://github.com/php-curl-class/php-curl-class
        </a>
    </li>
</ul>


</body>
</html>
