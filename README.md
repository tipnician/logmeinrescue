# LogMeInRescue

This class currently provides one function: formatting of Post-to-URL chat logs in a display-friendly manner. It takes the unformatted wall-of-text from LogMeIn and adds \<p> and \<span> tags so that the resulting output can be displayed on webpages or sent in HTML e-mails.

## Note: This class does NOT provide sanitizing or validation. 

# Installation

require_once('logmeinrescue.php');
$lmi = new logmeinrescue;

$chat_log = $lmi->formatChat($chat);

# Coming Soon?

Options! Lots of options! Well, sort of...

$lmi->chatClass(''); // sets the class name assigned to the entire chat

$lmi->techClass(''); // names the class for tech names

$lmi->timeClass(''); // names the class for time stamps

$lmi->custClass(''); // names the class for customer names

$lmi->systClass(''); // names the class for system messages

$lmi->setMethod('div'); // uses divs instead of spans (could break things)

// default behavior is to use spans


