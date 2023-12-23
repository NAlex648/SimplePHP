# Security
Browsing SimplePHP will take you to the index.php

From there, you can either register a new username and password, or use my mock/dummy account I provided

The login.php have several key security mechanisms, first, XSS and CSRF prevention, which I also added in register.php and home.php

I also set a rate limit to prevent brute force attempts, after 5 unsuccessful login attempt, it will lock you for 5 minutes before attempting again, successful attempt will reset it

They all have session cookie reset for session cookie hijacking prevention

I also add parallel login prevention system in my home.php and login.php, so if you try to go to home.php but not logging in, it will take you back. And if you try to login with an active session after logging in, it will move you back to home.php

23/12/2023: Added forgotten SQLi prevention for register, also adding function to prevent registering username already added to the database, to prevent twin accounts

# Add-on Feature
As of 16/12/2023, I've added artwork image upload function on home.php, planning on adding more features when I have time

17/12/2023: Added uploaded files listing/viewing
