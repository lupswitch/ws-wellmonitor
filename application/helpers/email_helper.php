<?php
/**
 * type 1 = html 0 = plain text
 */
function Send_Email($email,$subject,$message,$attach="",$type=0)
{
    try {
        if($type==1){
            $ch = curl_init($message);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $message = curl_exec($ch);
            curl_close($ch);
        }
        $this->email->clear(TRUE);
        $this->email->set_newline("\r\n");
        $this->email->from($this->config->item('emails_wellmonitor'));
        $this->email->to($email);
        $this->email->subject($subject);
        $this->email->message($message);
        if ($attach != "") $this->email->attach($attach);
        $this->email->send();  
    } catch (Exception $e) {
        $this->email->clear(TRUE);
        $this->email->set_newline("\r\n");
        $this->email->from('Wellmonitor.net');
        $this->email->to($this->config->item('emails_wellmonitor'));
        $this->email->subject("An error occurred while sending mail - Wellmonitor");
        $this->email->message("Error". $e->getMessage());
        $this->email->send();
        echo "Error sending the email to:" . "\n" . $this->email->print_debugger() . "'n";
    }
    return true;
}
?>