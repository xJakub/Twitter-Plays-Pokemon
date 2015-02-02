This is a tweet-based Twitch Plays Pokemon clone I made on February 21st, 2014. It consists of two programs:
* The **samp.php** script, which reads the commands through Twitter's Streaming API. Once parsed, they are added to **nextcommands.lua**.

* The VBA emulator, running the **main.lua** script. This script executes the content (keypad commands) within the **nextcommands.lua** file.