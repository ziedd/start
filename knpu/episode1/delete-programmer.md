# DELETE is for Saying Goodbye

So you have to part ways with your programmer, and we all know goodbyes are hard.
So let's delete them instead. We're going to create an rm -rf endpoint to send a 
programmer to `/dev/null`.

Start with the test! `public function testDELETEProgrammer`, because we'll
send a DELETE request to terminate that programmer resource:

[[[ code('9724a7b5a3') ]]]

Copy the guts of the GET test - fetching and deleting a programmer are almost
the same, except for the HTTP method, change it to `delete()`:

[[[ code('d5ecf72396') ]]]

Now, what's the status code? What should we return? We can't return the JSON
programmer, because we just finished truncating it's proverbial tables. I
mean, it's deleted - so it doesn't make sense to return the resource. Instead,
we'll return nothing and use a status code - 204 - that means "everything
went super great, but I have no content to send back." Remove the asserts
on the bottom... since there's nothing to look at:

[[[ code('378757befd') ]]]

## The Controller

Let's get straight to the controller: `public function deleteAction()`. Copy
the route stuff from `updateAction()`. It's all the same again, except the
method is different. Take out the route name - we don't need this unless we
link here. And change the method to `DELETE`:

[[[ code('bdc6e84a8c') ]]]

Grab the query code from `updateAction()` too, and make sure you have your
`$nickname` argument:

[[[ code('bf47eaf37b') ]]]

So this will 404 if we don't find the programmer. Surprise! In the REST world,
this is controversial! Since the job of this endpoint is to make sure the
programmer resource is deleted, some people say that if the resource is already
gone, then that's success! In other words, you should return the same 204
even if the programmer wasn't found. When you learn more about idempotency,
this argument makes some sense. So let's do it! But really, either way is
fine.

Change the `if` statement to be `if ($programmer)`, then we'll delete it.
Grab the EntityManager and call the normal `remove()` on it, then `flush()`:

[[[ code('18d80cfae6') ]]]

And whether the Programmer was found or not, we'll always return the same
`new Response(null, 204)`:

[[[ code('773a3df683') ]]]

Try the test and send a programmer to the trash! Filter for
`testDELETE`.

```bash
phpunit -c app --filter testPUTProgrammer
```

Another endpoint bites the dust!
