

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


Markers in the HTML-template
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

- You can use very much markers in the HTML-template. Here is a list of
  some of them.

.. ### BEGIN~OF~TABLE ###

.. container:: table-row

   Marker
         Marker:

   Usage for
         Usage for:


.. container:: table-row

   Marker
         TEMPLATE\_QUESTION\_PAGE

   Usage for
         Questions page


.. container:: table-row

   Marker
         REF\_ERRORS

   Usage for
         Empty or reference to TEMPLATE\_CAPTCHA\_NOT\_OK


.. container:: table-row

   Marker
         REF\_QUIZ\_LIMIT

   Usage for
         Reference to TEMPLATE\_QUIZ\_TIME\_LIMIT. REF\_QUIZ\_LIMIT will be
         empty if "quizTimeMinutes = 0"


.. container:: table-row

   Marker
         REF\_PAGE\_LIMIT

   Usage for
         Reference to TEMPLATE\_PAGE\_TIME\_LIMIT. REF\_PAGE\_LIMIT will be
         empty if "pageTimeSeconds = 0"


.. container:: table-row

   Marker
         FORM\_URL

   Usage for
         URL to the next page


.. container:: table-row

   Marker
         REF\_QUESTIONS

   Usage for
         Multiple reference to TEMPLATE\_QUESTION


.. container:: table-row

   Marker
         REF\_JOKERS

   Usage for
         Reference to TEMPLATE\_JOKERS. REF\_JOKERS will be empty if "useJokers
         = 0"


.. container:: table-row

   Marker
         REF\_SUBMIT\_FIELDS

   Usage for
         Reference to TEMPLATE\_QUIZ\_USER\_TO\_SUBMIT (userData.askAtQuestion
         = 1 or userData.askAtStart = 1), to TEMPLATE\_SUBMIT
         (userData.askAtQuestion = 0) or to TEMPLATE\_NO\_SUBMIT (if no rights)


.. container:: table-row

   Marker
         HIDDENFIELDS

   Usage for
         Hidden fields. Don´t remove!


.. container:: table-row

   Marker
         REF\_HIGHSCORE\_URL

   Usage for
         Reference to TEMPLATE\_HIGHSCORE\_URL. REF\_HIGHSCORE\_URL will be
         empty if is poll or dontShowHighscoreLink = 1 !!!


.. container:: table-row

   Marker
         REF\_POLLRESULT\_URL

   Usage for
         Reference to TEMPLATE\_POLLRESULT\_URL. REF\_POLLRESULT\_URL will be
         empty if isPoll = 0 !!!


.. container:: table-row

   Marker
         QUESTION

   Usage for
         Text


.. container:: table-row

   Marker
         MISSING\_ANSWER

   Usage for
         Text


.. container:: table-row

   Marker
         FE\_USER\_UID

   Usage for
         fe\_users uid


.. container:: table-row

   Marker
         QUIZ\_NAME

   Usage for
         Value of TS-variable quizName


.. container:: table-row

   Marker
         PAGE

   Usage for
         Current page no.


.. container:: table-row

   Marker
         MAX\_PAGES

   Usage for
         No. of total pages


.. container:: table-row

   Marker
         ###SUBMIT\_JSC###

   Usage for
         JavsScript. Don´t remove!


.. container:: table-row

   Marker


   Usage for


.. container:: table-row

   Marker
         TEMPLATE\_RESULT\_PAGE

   Usage for
         Results page


.. container:: table-row

   Marker
         REF\_RES\_ERRORS

   Usage for
         Empty or reference to TEMPLATE\_CHEATING


.. container:: table-row

   Marker
         REF\_INTRODUCTION

   Usage for
         Reference to TEMPLATE\_POLL\_SUBMITED (poll) or to
         TEMPLATE\_QUIZ\_USER\_SUBMITED (quiz; if "userData.showAtAnswer = 1",
         else empty)


.. container:: table-row

   Marker
         REF\_QRESULT

   Usage for
         Multiple reference to TEMPLATE\_QRESULT (empty if is poll or
         "dontShowCorrectAnswers=1")


.. container:: table-row

   Marker
         REF\_QPOINTS

   Usage for
         Reference to TEMPLATE\_RESULT\_POINTS or
         TEMPLATE\_RESULT\_POINTS\_TOTAL (if is quiz and "dontShowPoints=0",
         else empty)


.. container:: table-row

   Marker
         REF\_SKIPPED

   Usage for
         Reference to TEMPLATE\_SKIPPED (if "allowSkipping=1" and is quiz, else
         empty)


.. container:: table-row

   Marker
         REF\_NEXT

   Usage for
         Reference to TEMPLATE\_NEXT (if "showAnswersSeparate=1" and is quiz,
         else empty)


.. container:: table-row

   Marker
         REF\_POLLRESULT

   Usage for
         Reference to TEMPLATE\_POLLRESULT (if is poll and
         "dontShowPollResult=0", else empty)


.. container:: table-row

   Marker
         REF\_POLLRESULT\_URL

   Usage for
         Reference to TEMPLATE\_POLLRESULT\_URL. REF\_POLLRESULT\_URL will be
         empty if isPoll = 0


.. container:: table-row

   Marker
         REF\_HIGHSCORE\_URL

   Usage for
         Reference to TEMPLATE\_HIGHSCORE\_URL. REF\_HIGHSCORE\_URL will be
         empty if is poll or dontShowHighscoreLink = 1


.. container:: table-row

   Marker
         REF\_QUIZ\_LIMIT

   Usage for
         Reference to TEMPLATE\_QUIZ\_TIME\_LIMIT. REF\_QUIZ\_LIMIT will be
         empty if "quizTimeMinutes = 0"


.. container:: table-row

   Marker


   Usage for


.. container:: table-row

   Marker
         TEMPLATE\_QUIZ\_FINAL\_PAGE

   Usage for
         Final / last page


.. container:: table-row

   Marker
         REF\_ERRORS

   Usage for
         Empty or reference to TEMPLATE\_CAPTCHA\_NOT\_OK


.. container:: table-row

   Marker
         FORM\_URL

   Usage for
         URL to the highscore page


.. container:: table-row

   Marker
         REF\_NO\_MORE

   Usage for
         Reference to TEMPLATE\_NO\_MORE. REF\_NO\_MORE will be empty if
         pageQuestions = 0


.. container:: table-row

   Marker
         REF\_INTRODUCTION

   Usage for
         Reference to TEMPLATE\_QUIZ\_USER\_SUBMITED if userdata.showAtFinal =
         1, else empty


.. container:: table-row

   Marker
         REF\_QUIZ\_ANALYSIS

   Usage for
         Reference to TEMPLATE\_QUIZ\_ANALYSIS\_x or a page content. x depends
         on "showAnalysis". REF\_QUIZ\_ANALYSIS will be empty if showAnalysis =
         0 and showEvaluation = 0


.. container:: table-row

   Marker
         REF\_SUBMIT\_FIELDS

   Usage for
         Reference to TEMPLATE\_QUIZ\_USER\_TO\_SUBMIT if userdata.askAtFinal =
         1, else empty


.. container:: table-row

   Marker
         HIDDENFIELDS

   Usage for
         Don´t delete the marker HIDDENFIELDS


.. container:: table-row

   Marker
         REF\_HIGHSCORE

   Usage for
         Reference to TEMPLATE\_HIGHSCORE. REF\_HIGHSCORE will be empty if
         showHighscore = 0


.. container:: table-row

   Marker
         REF\_HIGHSCORE\_URL

   Usage for
         Reference to TEMPLATE\_HIGHSCORE\_URL. REF\_HIGHSCORE\_URL will be
         empty if dontShowHighscoreLink = 1


.. container:: table-row

   Marker
         RESTART\_QUIZ

   Usage for
         Link to restart the quiz


.. container:: table-row

   Marker
         RESET\_COOKIE

   Usage for
         Link to delete the cookie


.. container:: table-row

   Marker
         FE\_USER\_UID

   Usage for
         fe\_users uids


.. container:: table-row

   Marker


   Usage for


.. container:: table-row

   Marker
         TEMPLATE\_QUESTION

   Usage for
         Questions at the question-page


.. container:: table-row

   Marker
         REF\_QUESTION\_IMAGE\_BEGIN

   Usage for
         Reference to TEMPLATE\_QUESTION\_IMAGE\_BEGIN


.. container:: table-row

   Marker
         REF\_QUESTION\_IMAGE\_END

   Usage for
         Reference to TEMPLATE\_QUESTION\_IMAGE\_END


.. container:: table-row

   Marker
         REF\_DELIMITER

   Usage for
         Reference to TEMPLATE\_DELIMITER


.. container:: table-row

   Marker
         VAR\_QUESTION\_IMAGE

   Usage for
         Image of the question


.. container:: table-row

   Marker
         VAR\_QUESTION\_TITLE

   Usage for
         Title of a question


.. container:: table-row

   Marker
         VAR\_QUESTION\_NAME

   Usage for
         Name of a question


.. container:: table-row

   Marker
         P1 and P2

   Usage for
         () if VAR\_ANSWER\_POINTS if defined


.. container:: table-row

   Marker
         VAR\_ANSWER\_POINTS

   Usage for
         Points for every answer of this question


.. container:: table-row

   Marker
         VAR\_QUESTION\_POINTS

   Usage for
         Points for this question


.. container:: table-row

   Marker
         VAR\_NEXT\_POINTS

   Usage for
         Total points after this question


.. container:: table-row

   Marker
         VAR\_QUESTION

   Usage for
         Number of the question


.. container:: table-row

   Marker
         VAR\_QUESTIONS

   Usage for
         Number of total questions


.. container:: table-row

   Marker
         VAR\_QUESTION\_NUMBER

   Usage for
         Number of the question on the current page


.. container:: table-row

   Marker
         VAR\_QUESTION\_TYPE

   Usage for
         Question type (int)


.. container:: table-row

   Marker
         VAR\_QUESTION\_ANSWERS

   Usage for
         Number of answers for the current question


.. container:: table-row

   Marker
         VAR\_CATEGORY

   Usage for
         Current category


.. container:: table-row

   Marker
         VAR\_NEXT\_CATEGORY

   Usage for
         Next category


.. container:: table-row

   Marker
         TEMPLATE\_QUESTION\_ANSWER

   Usage for
         Here comes the answers of the question


.. container:: table-row

   Marker
         \- VAR\_QUESTION\_ANSWER

   Usage for
         Answer of a question


.. container:: table-row

   Marker
         \- VAR\_QA\_CATEGORY

   Usage for
         Category of an answer


.. container:: table-row

   Marker


   Usage for


.. container:: table-row

   Marker
         TEMPLATE\_QUESTION\_IMAGE\_BEGIN

   Usage for
         Image begin


.. container:: table-row

   Marker
         VAR\_QUESTION\_IMAGE

   Usage for
         Image of a question


.. container:: table-row

   Marker


   Usage for


.. container:: table-row

   Marker
         TEMPLATE\_QRESULT

   Usage for
         Questions of the results-page


.. container:: table-row

   Marker
         REF\_QUESTION\_IMAGE\_BEGIN

   Usage for
         Reference to TEMPLATE\_QUESTION\_IMAGE\_BEGIN


.. container:: table-row

   Marker
         REF\_QUESTION\_IMAGE\_END

   Usage for
         Reference to TEMPLATE\_QUESTION\_IMAGE\_END


.. container:: table-row

   Marker
         REF\_QR\_ANSWER\_CORR

   Usage for
         Reference to TEMPLATE\_QR\_CORR


.. container:: table-row

   Marker
         REF\_QR\_ANSWER\_ALL

   Usage for
         Reference to the next 4 templates in original order. Use it for polls or if you want to keep the original order.


.. container:: table-row

   Marker
         REF\_QR\_ANSWER\_CORR\_ANSW

   Usage for
         Reference to TEMPLATE\_QR\_CORR\_ANSW


.. container:: table-row

   Marker
         REF\_QR\_ANSWER\_CORR\_NOTANSW

   Usage for
         Reference to TEMPLATE\_QR\_CORR\_NOTANSW


.. container:: table-row

   Marker
         REF\_QR\_ANSWER\_NOTCORR\_NOTANSW

   Usage for
         Reference to TEMPLATE\_QR\_NOTCORR\_NOTANSW


.. container:: table-row

   Marker
         REF\_QR\_ANSWER\_NOTCORR\_ANSW

   Usage for
         Reference to TEMPLATE\_QR\_NOTCORR\_ANSW


.. container:: table-row

   Marker
         REF\_QR\_EXPLANATION

   Usage for
         Reference to TEMPLATE\_EXPLANATION


.. container:: table-row

   Marker
         REF\_QR\_POINTS

   Usage for
         Reference to TEMPLATE\_QR\_POINTS


.. container:: table-row

   Marker
         REF\_DELIMITER

   Usage for
         Reference to TEMPLATE\_DELIMITER


.. container:: table-row

   Marker
         TITLE\_HIDE

   Usage for
         -hide if the title is marked as hidden


.. container:: table-row

   Marker
         VAR\_QUESTION\_TITLE

   Usage for
         Title of the question


.. container:: table-row

   Marker
         VAR\_QUESTION\_NAME

   Usage for
         Name of the question


.. container:: table-row

   Marker
         VAR\_QUESTION

   Usage for
         Number of the question


.. container:: table-row

   Marker
         VAR\_QUESTIONS

   Usage for
         Number of total questions


.. container:: table-row

   Marker
         VAR\_QUESTION\_NUMBER

   Usage for
         Number of the question on the current page


.. container:: table-row

   Marker
         VAR\_CATEGORY

   Usage for
         Current category


.. container:: table-row

   Marker
         VAR\_NEXT\_CATEGORY

   Usage for
         Next category


.. container:: table-row

   Marker


   Usage for


.. container:: table-row

   Marker
         TEMPLATE\_QR\_CORR,

   Usage for
         A correct answer (answered and not answered


.. container:: table-row

   Marker
         TEMPLATE\_QR\_CORR\_ANSW,

   Usage for
         A correct answer which has been answered


.. container:: table-row

   Marker
         TEMPLATE\_QR\_CORR\_NOTANSW,

   Usage for
         A correct answer which has not been answered


.. container:: table-row

   Marker
         TEMPLATE\_QR\_NOTCORR\_ANSW,

   Usage for
         A not correct answer which has been answered


.. container:: table-row

   Marker
         TEMPLATE\_QR\_NOTCORR\_NOTANSW

   Usage for
         A not correct answer which has not been answered


.. container:: table-row

   Marker
         VAR\_QUESTION\_ANSWER

   Usage for
         Answer of the question


.. container:: table-row

   Marker
         VAR\_QA\_CATEGORY

   Usage for
         Category-name of this answer


.. container:: table-row

   Marker
         VAR\_QA\_NR

   Usage for
         Category-number of this answer


.. container:: table-row

   Marker
         P1, P2

   Usage for
         () if VAR\_ANSWER\_POINTS is defined


.. container:: table-row

   Marker
         VAR\_ANSWER\_POINTS

   Usage for
         Points for this answer


.. container:: table-row

   Marker


   Usage for


.. container:: table-row

   Marker
         TEMPLATE\_QR\_POINTS

   Usage for
         Points result for a question


.. container:: table-row

   Marker
         RES\_QUESTION\_POINTS

   Usage for
         Text


.. container:: table-row

   Marker
         VAR\_QUESTION\_POINTS

   Usage for
         Reached points for that question


.. container:: table-row

   Marker
         VAR\_MAX\_QUESTION\_POINTS

   Usage for
         Maximum points for that question


.. container:: table-row

   Marker


   Usage for


.. container:: table-row

   Marker
         TEMPLATE\_EXPLANATION

   Usage for
         Explanation for the question


.. container:: table-row

   Marker
         EXPLANATION

   Usage for
         Text


.. container:: table-row

   Marker
         VAR\_EXPLANATION

   Usage for
         Explanation


.. container:: table-row

   Marker


   Usage for


.. container:: table-row

   Marker
         TEMPLATE\_RESULT\_POINTS,

   Usage for
         Reached points for the first page


.. container:: table-row

   Marker
         TEMPLATE\_RESULT\_POINTS\_TOTAL

   Usage for
         Reached points for following pages


.. container:: table-row

   Marker
         RESULT\_POINTS

   Usage for
         Text


.. container:: table-row

   Marker
         VAR\_RESULT\_POINTS

   Usage for
         Reached points at that page


.. container:: table-row

   Marker
         VAR\_MAX\_POINTS

   Usage for
         Maximum points for that page


.. container:: table-row

   Marker
         TOTAL\_POINTS

   Usage for
         Text


.. container:: table-row

   Marker
         VAR\_TOTAL\_POINTS

   Usage for
         Reached points till now


.. container:: table-row

   Marker
         VAR\_TMAX\_POINTS

   Usage for
         Possible points till now


.. container:: table-row

   Marker
         VAR\_OMAX\_POINTS

   Usage for
         Possible reachable points at all


.. container:: table-row

   Marker
         VAR\_QUESTIONS\_ANSWERED

   Usage for
         Number of answered questions till now


.. container:: table-row

   Marker
         VAR\_QUESTIONS\_CORRECT

   Usage for
         Number of correct answered questions till now


.. container:: table-row

   Marker
         VAR\_QUESTIONS\_FALSE

   Usage for
         Number of false answered questions till now


.. container:: table-row

   Marker
         VAR\_MISSING\_POINTS

   Usage for
         Maximum – reached points for that page


.. container:: table-row

   Marker
         VAR\_TMISSING\_POINTS

   Usage for
         Maximum – reached points till now


.. container:: table-row

   Marker
         SO\_FAR\_REACHED1 and 2

   Usage for
         Text


.. container:: table-row

   Marker
         VAR\_OVERALL\_PERCENT

   Usage for
         Percentage of correct answered questions, depending on the points


.. container:: table-row

   Marker
         VAR\_CATEGORY

   Usage for
         Last category


.. container:: table-row

   Marker
         VAR\_NEXT\_CATEGORY

   Usage for
         Next category


.. container:: table-row

   Marker


   Usage for


.. container:: table-row

   Marker
         TEMPLATE\_QUIZ\_USER\_TO\_SUBMIT

   Usage for
         Submit fields with user data


.. container:: table-row

   Marker
         NAME, EMAIL, HOMEPAGE

   Usage for
         Text


.. container:: table-row

   Marker
         DEFAULT\_NAME, DEFAULT\_EMAIL, DEFAULT\_HOMEPAGE

   Usage for
         Default values or submitted values


.. container:: table-row

   Marker
         GO\_ON, RESET, GO\_BACK

   Usage for
         Text


.. container:: table-row

   Marker
         BACK\_STYLE

   Usage for
         style=”display:none;” if allowBack=0


.. container:: table-row

   Marker
         SR\_FREECAP\_NOTICE,

         SR\_FREECAP\_CANT\_READ, SR\_FREECAP\_IMAGE, SR\_FREECAP\_ACCESSIBLE

   Usage for
         sr\_freecap Captcha values in subpart CAPTCHA\_INSERT


.. container:: table-row

   Marker


   Usage for


.. container:: table-row

   Marker
         TEMPLATE\_QUIZ\_USER\_SUBMITED

   Usage for
         Submitted user values


.. container:: table-row

   Marker
         RESULT\_FOR, NAME, EMAIL, HOMEPAGE

   Usage for
         Text


.. container:: table-row

   Marker
         REAL\_NAME, REAL\_EMAIL, REAL\_HOMEPAGE

   Usage for
         Submitted user data


.. container:: table-row

   Marker


   Usage for


.. container:: table-row

   Marker
         TEMPLATE\_SUBMIT

   Usage for
         Submit fields without user data


.. container:: table-row

   Marker
         SUBMIT, RESET, GO\_BACK

   Usage for
         Text


.. container:: table-row

   Marker
         BACK\_STYLE

   Usage for
         style=”display:none;” if allowBack=0


.. container:: table-row

   Marker


   Usage for


.. container:: table-row

   Marker
         TEMPLATE\_NEXT

   Usage for
         Submit button to the next page


.. container:: table-row

   Marker
         FORM\_URL

   Usage for
         Next page


.. container:: table-row

   Marker
         GO\_ON, GO\_BACK

   Usage for
         Text


.. container:: table-row

   Marker
         BACK\_STYLE

   Usage for
         style=”display:none;” if allowBack=0


.. container:: table-row

   Marker
         QTUID

   Usage for
         Quiz taker ID


.. container:: table-row

   Marker


   Usage for


.. container:: table-row

   Marker
         TEMPLATE\_NO\_MORE

   Usage for
         No more questions left. Will be displayed at the end of the quiz


.. container:: table-row

   Marker
         NO\_MORE, CORRECT\_ANSWERS, YOUR\_EVALUATION, REACHED1, REACHED2,
         POINTS

   Usage for
         Text


.. container:: table-row

   Marker
         RESTART\_QUIZ

   Usage for
         Restart link


.. container:: table-row

   Marker
         RESET\_COOKIE

   Usage for
         Link to delete cookies


.. container:: table-row

   Marker
         VAR\_TOTAL\_POINTS

   Usage for
         Reached points


.. container:: table-row

   Marker
         VAR\_TMAX\_POINTS

   Usage for
         Possible (maximum) points


.. container:: table-row

   Marker
         VAR\_OVERALL\_PERCENT

   Usage for
         Solved answers in percentage


.. container:: table-row

   Marker
         In QUIZ\_ANSWERS:

   Usage for


.. container:: table-row

   Marker
         VAR\_QUESTION\_TITLE

   Usage for
         Question title


.. container:: table-row

   Marker
         VAR\_QUESTION\_NAME

   Usage for
         Question name


.. container:: table-row

   Marker
         REF\_QUESTION\_IMAGE\_BEGIN, REF\_QUESTION\_IMAGE\_END

   Usage for
         Image begin and end


.. container:: table-row

   Marker
         REF\_QR\_ANSWER\_CORR

   Usage for
         Reference to TEMPLATE\_QR\_CORR


.. container:: table-row

   Marker
         REF\_QR\_ANSWER\_ALL

   Usage for
         See TEMPLATE\_QRESULT


.. container:: table-row

   Marker
         REF\_QR\_EXPLANATION

   Usage for
         Reference to TEMPLATE\_EXPLANATION


.. container:: table-row

   Marker
         REF\_DELIMITER

   Usage for
         Reference to TEMPLATE\_DELIMITER


.. container:: table-row

   Marker
         VAR\_QUESTION

   Usage for
         Current question number


.. container:: table-row

   Marker
         VAR\_QUESTIONS

   Usage for
         Number of questions


.. container:: table-row

   Marker


   Usage for


.. container:: table-row

   Marker
         TEMPLATE\_ALLANSWERS

   Usage for
         Show all answers


.. container:: table-row

   Marker
         Like above (in QUIZ\_ANSWERS)

   Usage for
         See above


.. ###### END~OF~TABLE ######

