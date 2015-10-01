  function format_chat_log($customer_name,$chat_log)
    {

        /*
         *      Get some quick string replacements out of the way
         *
         */

        $find = array();
        $find[0] = 'The customer ended the session.';
        $find[1] = 'Disconnected (Applet)';
        $find[2] = 'The technician ended the session.';
        $find[3] = 'Switched to P2P';
        $count = count($find);
        $f = 0;

        while($f < $count) {
            $chat_log = str_replace($find[$f],'<span class="chat_sys">'.$find[$f].'</span>',$chat_log);
            $f++;
        }

        /*
         *      Regex is a bitch to learn.
         *
         */


        // 12:00 PM
        $time_regex = '/[0-9]*:[0-9][0-9] [AP]M /';
        // System Messages
        $lmi_regex_a = '/(Connecting to.*?\))/';
        $lmi_regex_b = '/(Connected to.*?\))/';

        // Joe Technician:
        $tech_regex = '#[AP]M]</span>(.*?:) #';
        // Jane User:
        $cust_regex = '/>(' . $customer_name . ':) /';

        $result = '';

        preg_match_all($time_regex, $chat_log, $count, NULL, 0);
        $iterations = count($count[0]);

        if ( $iterations > 0 ) {

            while ( $iterations > 0 ) {

                if ( $find = preg_match('/[0-9]*:[0-9][0-9] [AP]M /', $chat_log, $matches, NULL, 0) ) {

                    $target = $matches[0];

                    $ampm = ['AM ', 'PM '];
                    $ampm_replace = ['AM', 'PM'];
                    $result = str_replace($ampm, $ampm_replace, $target);
                    $result = str_replace($result, '[' . $result . ']</span>', $result);

                    // Now do the preg_replace on the first instance found.
                    $chat_log = preg_replace($time_regex, '<p><span class="log_time">'.$result, $chat_log, 1);
                }
                $iterations--;
            }
        }

        /*
         *      LogMeIn System Messages get styled second. We do this for a reason,
         *      mostly to do with how we're going to handle multiple technician names
         *      in a single chat (while logmein only reports one).
         */

        unset($count);
        unset($matches);


        /*
         *      Style the "Connecting to..." and "Connected to..." lines
         *
         */

        preg_match_all($lmi_regex_a, $chat_log, $matches, NULL, 0);

        // How many entries do we need to process?
        $count = count($matches[1]);

        // loop setup

        $i = 0;

        // Run it
        while($i < $count) {

            $chat_log = str_replace($matches[1][$i],'<span class="chat_sys">'.$matches[1][$i].'</span>',$chat_log);

            // Bump the counter so we address the next value in the $matches array. Loop stops when there are
            // no more items to process. Rendering should be complete!
            $i++;
        }

        unset($count);
        unset($matches);

        preg_match_all($lmi_regex_b, $chat_log, $matches, NULL, 0);

        // How many entries do we need to process?
        $count = count($matches[1]);

        // loop setup

        $i = 0;

        // Run it
        while($i < $count) {

            $chat_log = str_replace($matches[1][$i],'<span class="chat_sys">'.$matches[1][$i].'</span>',$chat_log);

            // Bump the counter so we address the next value in the $matches array. Loop stops when there are
            // no more items to process. Rendering should be complete!
            $i++;
        }


        /*
         *      Customer Name Replacement
         *
         *      After fixing the dates, we can use the customer's name from LogMeIn to
         *      add spans to the customer's name. This allows us to style these entries
         *      as we see fit, giving color and other font options in our CSS... and in
         *      the emailed receipt the customer receives.
         *
         *      DO NOT rearrange this function. Customer replacement comes first. See
         *      the "technician replacement" section after this for an explanation on
         *      why.
         *
         */

        unset($count);
        unset($matches);

        // Search for anything that looks like AM] or PM] ...characters... :
        // [1] holds the value we want ([0] holds the search string, including bracket and colon. not useful to us.

        preg_match_all($cust_regex, $chat_log, $matches, NULL, 0);

        // How many entries do we need to process?
        $count = count($matches[1]);

        // loop setup

        $i = 0;

        // Run it
        while($i < $count) {

            // Replaces 'Jane User' with <span class="chat_cust">Jane User</span>...

            $chat_log = str_replace($matches[1][$i],'<span class="chat_cust">'.$matches[1][$i].'</span>',$chat_log);

            // Bump the counter so we address the next value in the $matches array. Loop stops when there are
            // no more items to process. Rendering should be complete!
            $i++;
        }

        /*
         *      Technician name replacement
         *
         *      This comes last for a reason. That reason is multiple technicians in a session. LogMeIn only
         *      reports one technician when they send data to us, so we can't index off of TechName. If we
         *      did that, we'd get "wall of text" anywhere another tech did the talking.
         *
         *      Instead, we look for [AP]M] (AM or PM with a closing bracket. This is our starting point.
         *      Our regex then selects everything until it sees a colon ':'. This is almost universally
         *      a system message or technician name, so we can style them accordingly.
         *
         */

        // This will find the tech's name followed by a semicolon...

        unset($count);
        unset($matches);

        // Search for anything that looks like AM] or PM] ...characters... :
        // [1] holds the value we want ([0] holds the search string, including bracket and colon. not useful to us.

        preg_match_all($tech_regex, $chat_log, $matches, NULL, 0);

        // How many entries do we need to process?
        $count = count($matches[1]);

        // loop setup

        $i = 0;

        // Run it
        while($i < $count) {

            // Replaces 'Joe Technician' with <span class="chat_tech">Joe Technician</span>...

            $chat_log = str_replace($matches[1][$i],'<span class="chat_tech">'.$matches[1][$i].'</span>',$chat_log);

            // Bump the counter so we address the next value in the $matches array. Loop stops when there are
            // no more items to process. Rendering should be complete!
            $i++;
        }

        // Now we enclose the entire log in a general chat_txt class. This allows us to format all chat text,
        // except that the spans we added to the other entries will override. Voila, a beautifully formatted
        // and very flexible chat log!

        $chat_log = '<span class="chat_txt">'.$chat_log.'</span>';
        return $chat_log;

    }
