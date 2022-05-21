# counting.sh

> Please note that I'm not updating the code in this repository. I do accept new issues or anything similar about the project, though.

So we have a Discord channel, right? Where we just count upwards. Each person says one number, and is not allowed to count twice consecutively.

So naturally, I had an idea. Let's try to implement this mechanism but on my faculty's webserver!

> Are you a UBB FMI student? DM me to get the installation command, or just execute `install.sh` from this repo in your SCS console.

It uses a PHP script to track the counts, and also does some nifty identity verification. The PHP also does some concurrency stuff, that is in no way tested but hopefully just works. There are some weird things about the code which you should just accept, given how I've coded this at 1 AM.

I am interested in learning about ways to break the security of this app. Namely, it should be protected against:

- weirdly concurrent countings
- identity theft
- duplicate counting (the same user counting twice)
- tampering from outside (if you can't SSH into the server you shouldn't be able to change the counting in any way)
- any unexpected event which might crash the PHP script, fall outside the try/catch and leave the lockfile opened (is the latter even possible?)

If you can break any of these things, I am interested in learning how and buying you a drink.
