

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


Changelog
---------

- Version 2.6.5; 20.7.2017:

  TS votedOnly added. Thanks to xb for t3.

  Some optimations for TYPO3 7.
  
- Version 2.6.1; 23.11.2016:

  Bugfix: PHP 7 compatibility. Thanks to Willi Martens.

  Bugfix: Versioning related bug fixed. Thanks to Willi Martens.

- Version 2.6.0; 28.9.2016:

  TS email.answers added. Thanks to Marcel Utz.

- Version 2.5.2; 5.8.2016:

  2 bugs fixed: lang=-1 and empty category.

- Version 2.5.1; 17.4.2016:

  TYPO3 7 related bugfix version.

- Version 2.5.0; 17.3.2016:

  New version for TYPO3 6 and 7. Thanks to Gerald Loß.

- Version 2.4.5; 10.10.2015:

  Bug fixed: link to the template works now even there is only a reference to sys_file.

- Version 2.4.4; 30.5.2015:

  Bug fixed: localization in Typo3 6.
  Deprecated functions removed.
  no_cache-parameter removed.

- Version 2.4.1; 7.3.2015:

  new documentation format.

  OPT_ALL_POINTS added to Ajax-Result.

- Version 2.4.0; 24.12.2014:

  Some templates and styles changed (optimation for Bootstrap 3 and
  realURL).

  myvars.answers.input\_label 5 and 6 added (for Bootstrap 3).

- Version 2.3.4; 10.5.2014:

  Again Typo3 v.6.2 Bugfix-Release.

  ###TEMPLATE\_STAR\_RATING\_DETAILS\_ITEM### will not be used in Typo3
  6.2 (the details are fixed).

- Version 2.3.3; 10.4.2014:

  Typo3 v.6.2 Bugfix-Release.

- Version 2.3.2; 25.2.2014:

  Tow bugs fixed.

- Version 2.3.1; 20.7.2013:

  Bug with not sending an email fixed.

- Version 2.3.0; 16.7.2013:

  Keep selected answers when the captcha was wrong.

  Poll archive added. TS: cmd = archive.

  TS “pollStart” and “ignoreSubmits” added.

  New example template: template\_poll\_simplemodal.html

  Text-Emails now with the plain text of the HTML text.

  Small bug fixed with rating stars and other bug fixes.

- Version 2.2.0; 6.4.2013:

  Backend: normal statistics improved. Thanks to Marcel Utz.

  Possibility to reverse sorting order.

  TS “myVars.answers.input\_label = 2”, 3 or 4 now possible. Important for Bootstrap.

  TS “highscore.ignorePid” and “highscore.showUser” added.

  TS “blockIP” added.

  Now Typo3 6.0 compatible.

- Version 2.1.0; 9.2.2013:

  Some template improvements (e.g. with jokers).

  Small fix with the star-rating.

  Font-family and some colors in the default-css-file removed. Font-size changed.

  New TypoScript: myVars.separator.

  New HTML-examples: template\_poll\_jquery\_pie.html and
  template\_poll\_jquery\_bar.html.

  Code cleaning. This changelog shortened.

- Version 2.0.6; 5.2.2013:

  Backend module: some functions are restricted for the admin-mode.

  **Important security fix.**

- Version 2.0.0; 7.9.2012:

  New question-type added: star rating. New TS: starRatingDetails and
  alwaysShowStarRatingDetails. Thanks to Marcel Utz.

  Star rating files can be included via static typoscript.

  Possibility to hide user-results in the backend. quizpoll\_hidden added to backend style sheet.

  Possibility to go back, when pageQuestions>1. New TS and Flexform: allowBack.

  Possibility to give a default answer if a question is a text-answer.

  New layout for user data. New style sheets added.

  New example-template: template\_poll\_multipage.html.

  New TS and flexform option: hideByDefault.

  New TS: myVars.page, requireSession.

  TS “disbaleIp” renamed to “disableIp”!
