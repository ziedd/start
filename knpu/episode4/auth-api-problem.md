# JSON Errors in your API

Whenever something goes wrong in our API, we have a *great* setup: we always get
back a descriptive JSON structure with keys that describe what went wrong:

[[[ code('1de57b07c9') ]]]

I want to do the exact same thing when something goes wrong with authentication.

Open up the `TokenControllerTest`:

[[[ code('b167c530e4') ]]]

Here, we purposefully send an *invalid* username and password combination.
This actually hits `TokenController`, we throw this new `BadCredentialsException`
and that kicks us out:

[[[ code('aaa66782b2') ]]]

It turns out that doing this this *also* triggers the entry point. And if you think
about it, that makes sense: any time an anonymous user is able to get into your
application:

[[[ code('bfebb8b69d') ]]]

And then you throw an exception to deny access, that will trigger the entry
point. And our entry point is *not* yet returning the nice API problem structure.

## Testing for the API Problem Response

Copy the last four lines from one of the tests in `ProgrammerControllerTest`:

[[[ code('a29e25480a') ]]]

And add that to `testPostTokenInvalidCredentials()`:

[[[ code('f87215f2f6') ]]]

The header should be `application/problem+json`. The type should be `about:blank`:
that's what you should use when the status code - 401 here - already fully describes
what went wrong. For the `title` use `Unauthorized` - that's the standard text that
always goes with a 401 status code. The `ApiProblem` class will actually set that
for us: when we pass a `null` type, it sets `type` to `about:blank` and looks up
the correct `title`.

Finally, for `detail` - which is an optional field for an API problem response - use
`Invalid Credentials.` with a period. I'll show you *why* we're expecting that in
a second.

## ApiProblem in start()

Head to the `JwtTokenAuthenticator`. In `start()`, create a new `$apiProblem = new ApiProblem()`.
Pass it a 401 status code with no `type`:

[[[ code('b6fc8f9040') ]]]

The `detail` key should tell the API client *any* other information about what went
wrong. And check this out: when the `start()` method is called, it has an optional
`$authException` argument. Most of the time, when Symfony calls `start()` its because
an `AuthenticationException` has been thrown. And *this* class gives us some information
about *what* caused this situation.

And in fact, in `TokenController`, we're throwing a `BadCredentialsException`, which
is a sub-class of `AuthenticationException`. Hold `command` to look inside the class:

[[[ code('001d78098d') ]]]

It has a `getMessageKey()` method set to `Invalid Credentials.`: make sure you test
matches this string exactly:

[[[ code('7bef01d951') ]]]

The `AuthenticationException` - and its sub-classes - are special: each has a
`getMessageKey()` method that you can safely return to the user to help *hint* as
to what went wrong.

Add `$message = $authException ? $authException->getMessageKey() : 'Missing Credentials';`:

[[[ code('03997258fc') ]]]

If no `$authException` is passed, this is the best message we can return to the client.
Finish this with `$apiProblem->set('details', $message)`.:

[[[ code('12ce038c5a') ]]]

Finally, return a `new JsonResponse` with `$apiProblem->toArray()` and then a 401:

[[[ code('90c69584c4') ]]]

Perfect! Well, not *actually* perfect, but it's getting close.

Copy the invalid credentials test method and run:

```bash
./vendor/bin/phpunit --filter testPOSTTokenInvalidCredentials
```

It's close! The response looks right, but the `Content-Type` header is `application/json`
instead of the more descriptive `application/problem+json`.

Well that's no problem! We just need to set the header inside of the `start()`
method. But wait! Don't do that! Because we've done all of this work before.
