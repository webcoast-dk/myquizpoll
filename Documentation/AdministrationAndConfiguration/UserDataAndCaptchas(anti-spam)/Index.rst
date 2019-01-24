

.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. ==================================================
.. DEFINE SOME TEXTROLES
.. --------------------------------------------------
.. role::   underline
.. role::   typoscript(code)
.. role::   ts(typoscript)
   :class:  typoscript
.. role::   php(code)


User data and Captchas (anti-spam)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

You can use the captchas only when you enable the ask for user data
(name, email and homepage). You can ask for the user data on an extra
start page, on every question page or on the special final page.
Enable it with “enableCaptcha=1”.

Example 1: you want to ask for the user data and captcha only on an
extra start page. Then you have to set “userData.askAtStart=1” . This
works fine, but if you set the “nextPID” too, it would not work
anymore. In that case you must define the “startPID” on the second
page too. If the captcha is not OK, a redirect back to the start page
will be made.

Example 2: you want to ask for the captcha at every page. Then you
have to set “userData.askAtQuestion=1” .

Example 3: you want to ask for the user data only on the final page.
Then you have to set “userData.askAtFinal=1” and you must set the
“listPID” too. After the final page you will go to the “listPID”-page.
The user data will be saved only at a “highscore list”-page. If you
don´t need a highscore list, delete it in the template.

