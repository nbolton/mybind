<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

?>

<h2>MyBind Help</h2>

<h3>Name servers</h3>
<p>
  On your domain registrar control panel, use the following name servers for
  your domain name:
  <ul>
    <li>ns1.mybind.com</li>
    <li>ns2.mybind.com</li>
  </ul>
</p>

<h3>Example zone</h3>
<table class="example">
  <tr>
    <th>Name</th>
    <th>Type</th>
    <th>Aux</th>
    <th>Data</th>
  </tr>
  <tr>
    <td>@</td>
    <td>A</td>
    <td></td>
    <td>173.194.34.169</td>
  </tr>
  <tr>
    <td>www</td>
    <td>CNAME</td>
    <td></td>
    <td>@</td>
  </tr>
  <tr>
    <td>mail</td>
    <td>A</td>
    <td></td>
    <td>65.55.58.201</td>
  </tr>
  <tr>
    <td>@</td>
    <td>MX</td>
    <td>10</td>
    <td>mail</td>
  </tr>
</table>
<p>
  In this example, the first A record points to your server IP. The 2nd
  CNAME record just points back to the 1st A record. The 3rd A record
  points to your mail server. Finally, the 4th MX record just points
  back to the 3rd A record.
</p>

<h3>Common BIND errors</h3>
<table>
  <tr>
    <th>Error</th>
    <th>Meaning</th>
  </tr>
  <tr>
    <td>multiple RRs of singleton type</td>
    <td>Zone may have multiple CNAME records for a given name.</td>
  </tr>
  <tr>
    <td>bad dotted quad</td>
    <td>A record does not have IP address value.</td>
  </tr>
  <tr>
    <td>not a valid number</td>
    <td>MX record missing aux value (priority).</td>
  </tr>
</table>

<h3>Use of dots/periods</h3>
<p>
  The dot/period is useful when you want to use a fully qualified domain
  name (FQDN) in the data field. For example, if you wanted to use
  Google's mail server, then you would use <code>ghs.google.com.</code>
  in your data field (note the trailing dot/period).
</p>
<p>For example:</p>
<table class="example">
  <tr>
    <th>Name</th>
    <th>Type</th>
    <th>Data</th>
  </tr>
  <tr>
    <td>mail</td>
    <td>CNAME</td>
    <td>ghs.google.com.</td>
  </tr>
</table>

<h3>TTL and caching</h3>
<p>
  It's a common misconception that all DNS updates take 24 hours to update.
  This depends entirely on your TTL for the zone (or individual records).
  TTL stands for "time to live" (how long the data can live for as cache).
  With MyBind, you choose the TTL that works for you. When a DNS client (or an
  ISP's DNS server) downloads your zone data, it will cache it for as long
  as the TTL; lets say it's 300 seconds. After 300 seconds of being in the
  DNS client's cache, it will become stale and will no longer be used.
</p>
<p>
  Adjusting the TTL can be useful for migrating websites to a different
  server with a new IP, where the site is using a database, and the old
  database must not be updated. You simply adjust the TTL to 120 seconds
  (2 minutes, which is the RFC recommended minimum), wait for any existing
  DNS client cache to expire, and then change the IP on the A record. This
  way, your maximum downtime is only 2 minutes. You could set the TTL to
  1 second if you like, but this is sometimes regarded as risky.
</p>

<h3>Tips &amp; Tricks</h3>
<p>
  Use the <a href="http://dig.menandmice.com/knowledgehub/tools/dig" target="_blank">Men &amp; Mice DIG online</a>
  website to test your DNS records - it's pretty good.
</p>
