<?php
/*
Plugin Name: CF7 for Google Sheets
Description: Cохранение почты в гугл таблицы
Version: 0.1
Author: Гаджибеков Арсен
*/

require_once 'vendor/autoload.php';
require_once 'Wingi_Google_Service.php';


class WingiMailToTable{

   private
        $send_data = array(),
        $additionData = array(),
        $message = array(),
        $spreadsheetId,
        $title,
        $form_names;

   public function __construct(){
       $this->getOptions();

       add_action( 'admin_menu' ,            array(&$this, 'settingPage') );                   // 1

       add_filter( "wpcf7_posted_data",      array( &$this, 'get_field_value' ) );             // 2
       add_action( "wpcf7_before_send_mail", array( &$this, "get_field_shortcode" ) );         // 3 CALLING
       add_filter( 'wpcf7_mail_components',  array( &$this, "getMessage" ) );                // 4

       add_action( "wpcf7_mail_sent",        array( &$this, "action_wpcf7_mail_sent" ) );   // 5
   }

    // START SETTINGS
    public  function settingPage() {
       add_options_page(
           'Настройки плагина Mail To Table',
           'Настройки  Mail To Table',
           'manage_options',
           __FILE__,
           array(&$this, "mailToTableSettingPage")
       );
    }

    public function mailToTableSettingPage(){
        if ($_POST['spreadsheetId'])
            update_option('spreadsheetId', $_POST['spreadsheetId']);
       ?>
            <div class="wrap">
            <h2>Настройки Mail To Google Docs</h2>

            <form method="post" action="" id="mail_to_table">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">SPREADSHEET_ID</th>
                        <td>
                            <input style="width:60%;" name="spreadsheetId"
                                   value="<?php echo get_option('spreadsheetId'); ?>"/>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    private function getOptions() {
        $this->spreadsheetId =  trim( get_option('spreadsheetId') );
    }

    //END SETTINGS


    // Get data for field
    public function get_field_value( $cf_7 ){
        error_log("POST DATA");
        $this->send_data = $cf_7; 
        return $cf_7;
    }

    public function get_field_shortcode($WPCF7_ContactForm){
        error_log("Before send mail");
        $this->title = $WPCF7_ContactForm->title();
    }




    public function getMessage($components) {

        $this->additionData['sender'] = $components['subject'];

        if ( is_array( $components['attachments']) &&  !empty($components['attachments'][0]) ) {
            $this->additionData['file'] = $components['attachments'][0];
        }
        else{
            $this->additionData['file'] = "---";
        }

        return $components;
    }


    public  function  runFunc( $spreadsheetId, $sendData, $title  ) {
        error_log("runFunc");
        $this->additionData['file'] = str_replace( '/var/www/compumur/data/www/', '' , $this->additionData['file'] );

        $this->message = array(
             $this->check_isset($sendData['your-name']),
             $this->check_isset($sendData['your-email']),
             $this->check_isset($this->additionData['sender']),
             $this->check_isset($sendData['your-message']),
             $this->check_isset($this->additionData['file'])
        );

        try{
            $this->addSheets($spreadsheetId,  $this->message , $title );
        }
        catch(Exception $ex) {
            error_log( print_r( $ex , true) );
        }
    }

    // Add message to google sheet
    private function addSheets ( $spreadsheetId, $message, $title ) {

        if ( isset( $message ) && is_array( $message ) ){
            error_log( "addSheets" );
            $sheets_obj = new Wingi_Google_Service();

            $message[] = date("d.m.y:H:i:s");
            $sheets_obj->appendDataCell(
                $spreadsheetId,
                $title."!A1:F1",
                $message
            );
        }
        else{
            error_log(' MESSAGE FIELD ARE EMPTY');
        }
    }

    public function action_wpcf7_mail_sent(  ) {
        $this->runFunc( $this->spreadsheetId,  $this->send_data,  $this->title );
    }

    private function check_isset($data){
        if ( isset($data) && !empty($data) )
            return $data;
        else
            return "--";
    }
}

new WingiMailToTable();