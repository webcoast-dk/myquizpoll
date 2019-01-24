

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


All about scores and the correct-checkbox
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

If you don´t need scores in your quiz, you should disable them with
“dontShowPoints = 1”. If you need scores, but you want show them in
the front end, don´t set “dontShowPoints = 1”! Change only the
templates! “dontShowPoints = 1” will disable scores calculations too.

If you set the  **scores for each answer** , then the quiz-program
will ignore the scores set for a question. Furthermore the program
will ignore the correct-checkbox. Answers with scores > 0 will be
treated as if the correct-checkbox is clicked.And note too: no
negative scores are then available!Exclusion: scores for each answers
will be ignored if you set noNegativePoints=3 or 4.

Furthermore: if you set noNegativePoints=3 or 4, then the quiz taker
will get in every case only once scores (not for every correct
answer)!

If you use the type “ **text input** ”, then the correct-checkbox will
be ignored too. The field with the text (normally answer 1) will be
treated as if the correct-checkbox is clicked.

Important notice about the “ **correct/false answered questions** ”
information: only if you check a correct-checkbox in the backend,
answers will be evaluated. A questions without a checked correct-
checkbox will be ignored therefor. Questions which have not been
answered by the quiz taker will be ignored too - can be changed with
TS-variable “noAnswer”!

