<?php

$home = 'Karlsruhe';

if (isset($_GET['home']) && $_GET['home'] !== "") {
    $home = urlencode($_GET['home']);
}

echo '<?xml version="1.0" encoding="UTF-8" ?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
     <ShortName>Bahnsearch (home: ' . $home . ')</ShortName>
     <Description>Bahnsearch (home: ' . $home . ')</Description>
     <InputEncoding>UTF-8</InputEncoding>
     <Url type="text/html" template="http://www.florianmeinel.de/projects/bahnsearch/?home=' . $home . '&amp;q={searchTerms}"/>
</OpenSearchDescription>';

?>
