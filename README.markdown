# EmailYak Client for PHP 
-----------------------------
A PHP client for the [EmailYak][1] API. Uses `Zend_Http_Client`, so the 
[ZendFramework][2] (or at least a portion if it) is required.

Currently the API is in beta, so things may very well change. Even more so, this 
client is in development, and may change.

[1]: http://www.emailyak.com/
[2]: http://framework.zend.com/

# Install
-----------------------------
Place the library/EmailYak inside your include directory.

# Usage
-----------------------------
Note: This client is in development, use accordingly. 

The basic usage follows the [API methods][3]. 

`get($path, $params) and post($path, $params)` may be used to directly access the 
API. The path is URI portion following the version/key/format/.

`getEmails($emailId, $headers)` is used to retrieve one or more messages. For a 
single message use a string Id, for multiple messages use an array of Ids.

`getEmails($start, $end, $domain, $headers, $new)` is used to retrieve a list of
emails. All parameters are optional.

`getAllEmail($start, $end, $domain, $headers)` is an alias of `getEmails()` with 
`$new` set to false.

`getNewEmail($start, $end, $domain, $headers)` is an alias of `getEmails()` with
`$new` set to true.

`sendEmail($to, $from, $subject, $textBody, $htmlBody, $headers)` will send an
email. `$htmlBody` and `$headers` are optional.

`registerAddress($address, $callback)` and `registerDomain($domain, $callback)` will
register an address or domain and an optional callback url.

An `EmailYak_Exception` is thrown for any API error.

[3]: http://docs.emailyak.com/

# License
-----------------------------
See [LICENSE](http://github.com/tjlytle/EmailYak/blob/master/LICENSE).
