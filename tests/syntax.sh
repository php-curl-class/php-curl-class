# FIXME: Use ruleset.xml
phpcs ../src/Curl.class.php | \
    grep -v "| ERROR   | Expected \"} else {\\\n\"; found \"}\\\n" | \
    grep -v "| ERROR   | Missing class doc comment"                | \
    grep -v "| ERROR   | Missing file doc comment"                 | \
    grep -v "| ERROR   | Missing function doc comment"             | \
    grep -v "| ERROR   | Opening brace should be on a new line"    | \
    grep -v "| WARNING | Line exceeds 85 characters;"
