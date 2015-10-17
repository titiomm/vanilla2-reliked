<?php if (!defined('APPLICATION')) exit();

// Define the plugin:
$PluginInfo['ReLiked'] = array(
    'Name' => 'ReLiked',
    'Description' => 'Adds the facebook like and/or share feature to your discussions.',
    'Version' => '1.0',
    'SettingsPermission' => 'Garden.AdminUser.Only',
    'SettingsUrl' => '/dashboard/settings/reliked',
    'RequiredTheme' => FALSE,
    'RequiredPlugins' => FALSE,
    'HasLocale' => FALSE,
    'MobileFriendly' => TRUE,
    'Author' => "Thomas Cuvillier",
    'AuthorEmail' => 'thomas@tualaweb.fr',
    'AuthorUrl' => 'http://www.tualaweb.fr',
    'License' => 'GPLv2'
);

class ReLikedPlugin extends Gdn_Plugin {

    private $Code;

    function __construct(){
        //On init construct button's code
        $this->Code = '<div class="RL_fb_like"><div class="fb-like" data-href="%s" data-layout="'.C('Plugins.ReLiked.Layout').'" data-action="like" data-share="'.C('Plugins.ReLiked.Share').'" data-show-faces="'.C('Plugins.ReLiked.ShowFaces').'"></div></div>';
    }

    //Before Discussion Hook
    public function DiscussionController_BeforeDiscussionBody_Handler($Sender) {
        //If position is not bottom only
        if(C('Plugins.ReLiked.Position') != 2){
            echo sprintf($this->Code, Gdn_Url::Request(true, true, true));
        }
    }

    //After Discussion Hook
    public function DiscussionController_AfterDiscussionBody_Handler($Sender) {
        //If position is not top only
        if(C('Plugins.ReLiked.Position') != 1){
            echo sprintf($this->Code, Gdn_Url::Request(true, true, true));
        }
    }

    public function Base_AfterBody_Handler($Sender) {
        if(C('Plugins.ReLiked.InitScript') == FALSE){
            echo '<script src="//connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v2.3"></script>';
        }
    }

    //Settings Hook
    public function PluginController_ReLiked_Create($Sender) {
        //Settings page
        $Sender->Permission('Garden.Plugins.Manage');
        $Sender->AddSideMenu();
        $Sender->Title('ReLiked Settings');
        $ConfigurationModule = new ConfigurationModule($Sender);
        $ConfigurationModule->RenderAll = True;
        $Schema = array(
            'Plugins.ReLiked.InitScript' =>
            array('LabelCode' => T('Disable insertion of facebook\'s simple init script (ie: redundant, compatibility issues)'),
                'Control' => 'CheckBox',
                'Default' => C('Plugins.ReLiked.InitScript', '0')
            ),
            'Plugins.ReLiked.Layout' =>
            array('LabelCode' => T('Layout *'),
                'Control' => 'DropDown',
                'Default' => C('Plugins.ReLiked.Layout', 'standard'),
                'Items' => array('standard' => 'Standard', 'button' => 'Button', 'box_count' => 'Box Count')
            ),
            'Plugins.ReLiked.Position' =>
            array('LabelCode' => T('Position *'),
                'Control' => 'DropDown',
                'Default' => C('Plugins.ReLiked.Position', 'standard'),
                'Items' => array('1' => 'Top', '2' => 'Bottom', 3 => 'Top & Bottom')
            ),
            'Plugins.ReLiked.Share' =>
            array('LabelCode' => T('Show the share button'),
                'Control' => 'CheckBox',
                'Default' => C('Plugins.ReLiked.Share', '0')
            ),
            'Plugins.ReLiked.ShowFaces' =>
            array('LabelCode' => T('Show user\'s faces'),
                'Control' => 'CheckBox',
                'Default' => C('Plugins.ReLiked.ShowFaces', '0')
            )
        );
        $ConfigurationModule->Schema($Schema);
        $ConfigurationModule->Initialize();
        $Sender->View = dirname(__FILE__) . DS . 'views' . DS . 'settings.php';
        $Sender->ConfigurationModule = $ConfigurationModule;
        $Sender->Render();
    }
}

