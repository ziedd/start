# Finish POST with a Form

To create a programmer, our client needs to send up some data. And while you
can send that data as JSON, XML, form-encoded or any insane format you dream
up, you'll probably want your clients to send JSON... unless you work for
the banking or insurance industry. They love XML.

For the JSON, we can design it to have any keys. But since our Programmer
entity has the properties `nickname`, `avatarNumber` and `tagLine`, let's
use those.

These don't have to be the same, but it makes life easier if you can manage it.

Back in `testing.php`, create a `$nickname` - but make it a little bit random:
this has a unique index in the database and I don't want everything to blow
up if I run the file twice. Make a `$data` array and put everything
in it. The `avatarNumber` is *which* built-in avatar you want - it's a number
from 1 to 6. And add a `tagLine`:

[[[ code('c915117b0b') ]]]

To send this data, add an options array to `post`. It has a key called `body`,
and it's literally the raw string you want to send. So we need to `json_encode($data)`:

[[[ code('f5428f6c99') ]]]

## Reading the Request Body

This looks good - so let's move to our controller. To *read* the data the
client is sending, we'll need the `Request` object. So add that as an argument:

[[[ code('e3c4b11427') ]]]

To get the JSON string, say `$body = $request->getContent()`. And to prove
things are working, just return the POST'd body right back in the response:

[[[ code('74934ecfd9') ]]]

The client is sending a JSON string and our response is just sending that
right back. Try it!

```bash
php testing.php
```

Hey, that's prefect! We get a 200 status code response and its content is
the JSON we sent it. Time to pack it up and call it a day. Just kidding.

## Create the Programmer

Now that we've got the JSON, creating a `Programmer` is ridiculously simple.
First, `json_decode` the `$body` into an array:

[[[ code('4b0df0eef5') ]]]

For now, we'll trust the JSON string has a valid structure. And the second
argument to `json_decode` makes sure we get an array, not a `stdClass` object.

Now for the most obvious code you'll see `$programmer = new Programmer()`,
and pass it `$data['nickname']` and `$data['avatarNumber']` - I gave this
entity class a `__construct()` function with a few optional arguments. Now,
`$programmer->setTagLine($data['tagLine'])`:

[[[ code('ce36894f4c') ]]]

The only tricky part is that the `Programmer` has a relationship to the `User`
that created it, and this is a required relationship. On the web, I'm logged
in, so that controller sets my `User` object on this when I create a `Programmer`. 
But our API doesn't have any authentication yet - it's all anonymous.

We'll add authentication later. Right now, we need a workaround. Update the
controller to extend `BaseController` - that's something *I* created right
in `AppBundle/Controller` that just has some handy shortcut methods. This
will let me say `$programmer->setUser($this->findUserByUsername('weaverryan'))`:

[[[ code('467b910f14') ]]]

So we're cheating big time... for now. At least while developing, that user
exists because it's in our fixtures. I'm not proud of this, but I promise
it'll get fixed later.

Finish things off by persisting and flushing the Programmer:

[[[ code('10c3975d70') ]]]

Enjoy this easy stuff... while it lasts. For the `Response`, what should we
return? Ah, let's worry about that later - return a reassuring message, like
`It worked. Believe me, I'm an API!`:

[[[ code('1138d9b2b4') ]]]

The whole flow is there, so go back and hit the script again:

```bash
php testing.php
```

And... well, I think it looks like that probably worked. Now, let's add a
form.
