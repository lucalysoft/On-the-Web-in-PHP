<?php

use org\bovigo\vfs\vfsStream;
use SuiteCRM\Tests\SuiteCRM\Test\SuitePHPUnitFrameworkTestCase;

require_once 'include/utils/file_utils.php';
class file_utilsTest extends SuitePHPUnitFrameworkTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->rootFs = org\bovigo\vfs\vfsStream::setup('root');
        $this->rootFs->addChild(org\bovigo\vfs\vfsStream::newDirectory('testDir'));
        $this->rootFs->addChild(org\bovigo\vfs\vfsStream::newFile('test.txt')->withContent('Hello world!'));
    }

    public function testclean_path(): void
    {
        //execute the method and test if it returns expected values

        //invalid path
        $expected = '';
        $path = '';
        $actual = clean_path($path);
        self::assertSame($expected, $actual);

        //a simple valid path
        $expected = '/SuiteCRM-develop/include/utils';
        $path = '\SuiteCRM-develop\include\utils';
        $actual = clean_path($path);
        self::assertSame($expected, $actual);

        //valid network path
        $expected = '//SuiteCRM-develop/include/utils';
        $path = '\\\\/SuiteCRM-develop/include/utils';
        $actual = clean_path($path);
        self::assertSame($expected, $actual);

        $expected = '/SuiteCRM-develop/include/utils';
        $path = '/SuiteCRM-develop/./include/utils';
        $actual = clean_path($path);
        self::assertSame($expected, $actual);

        $expected = '/SuiteCRM-develop/include/utils';
        $path = '/SuiteCRM-develop//include/utils';
        $actual = clean_path($path);
        self::assertSame($expected, $actual);
    }

    public function testcreate_cache_directory(): void
    {
        //execute the method and test if it created file/dir exists

        $cache_dir = rtrim($GLOBALS['sugar_config']['cache_dir'], '/\\');
        $file = 'Test/';

        if ($this->rootFs->hasChild($file)  == true) {
            rmdir($cache_dir.'/'.$file);
        }

        $actual = create_cache_directory($file);
        self::assertFileExists($actual);

        if ($this->rootFs->hasChild($file)  == true) {
            rmdir($cache_dir.'/'.$file);
        }
    }

    public function testget_module_dir_list(): void
    {
        //execute the method and test if it returns expected values

        $expected = array(
                'Accounts' => 'Accounts',
                'ACL' => 'ACL',
                'ACLActions' => 'ACLActions',
                'ACLRoles' => 'ACLRoles',
                'Activities' => 'Activities',
                'Administration' => 'Administration',
                'Alerts' => 'Alerts',
                'AM_ProjectTemplates' => 'AM_ProjectTemplates',
                'AM_TaskTemplates' => 'AM_TaskTemplates',
                'AOBH_BusinessHours' => 'AOBH_BusinessHours',
                'AOD_Index' => 'AOD_Index',
                'AOD_IndexEvent' => 'AOD_IndexEvent',
                'AOK_KnowledgeBase' => 'AOK_KnowledgeBase',
                'AOK_Knowledge_Base_Categories' => 'AOK_Knowledge_Base_Categories',
                'AOP_Case_Events' => 'AOP_Case_Events',
                'AOP_Case_Updates' => 'AOP_Case_Updates',
                'AOR_Charts' => 'AOR_Charts',
                'AOR_Conditions' => 'AOR_Conditions',
                'AOR_Fields' => 'AOR_Fields',
                'AOR_Reports' => 'AOR_Reports',
                'AOR_Scheduled_Reports' => 'AOR_Scheduled_Reports',
                'AOS_Contracts' => 'AOS_Contracts',
                'AOS_Invoices' => 'AOS_Invoices',
                'AOS_Line_Item_Groups' => 'AOS_Line_Item_Groups',
                'AOS_PDF_Templates' => 'AOS_PDF_Templates',
                'AOS_Products' => 'AOS_Products',
                'AOS_Products_Quotes' => 'AOS_Products_Quotes',
                'AOS_Product_Categories' => 'AOS_Product_Categories',
                'AOS_Quotes' => 'AOS_Quotes',
                'AOW_Actions' => 'AOW_Actions',
                'AOW_Conditions' => 'AOW_Conditions',
                'AOW_Processed' => 'AOW_Processed',
                'AOW_WorkFlow' => 'AOW_WorkFlow',
                'Audit' => 'Audit',
                'Bugs' => 'Bugs',
                'Calendar' => 'Calendar',
                'Calls' => 'Calls',
                'Calls_Reschedule' => 'Calls_Reschedule',
                'CampaignLog' => 'CampaignLog',
                'Campaigns' => 'Campaigns',
                'CampaignTrackers' => 'CampaignTrackers',
                'Cases' => 'Cases',
                'Charts' => 'Charts',
                'Configurator' => 'Configurator',
                'Connectors' => 'Connectors',
                'Contacts' => 'Contacts',
                'Currencies' => 'Currencies',
                'Delegates' => 'Delegates',
                'DocumentRevisions' => 'DocumentRevisions',
                'Documents' => 'Documents',
                'DynamicFields' => 'DynamicFields',
                'EAPM' => 'EAPM',
                'EmailAddresses' => 'EmailAddresses',
                'EmailMan' => 'EmailMan',
                'EmailMarketing' => 'EmailMarketing',
                'Emails' => 'Emails',
                'EmailTemplates' => 'EmailTemplates',
                'EmailText' => 'EmailText',
                'Employees' => 'Employees',
                'Favorites' => 'Favorites',
                'FP_events' => 'FP_events',
                'FP_Event_Locations' => 'FP_Event_Locations',
                'Groups' => 'Groups',
                'Help' => 'Help',
                'History' => 'History',
                'Home' => 'Home',
                'iCals' => 'iCals',
                'Import' => 'Import',
                'InboundEmail' => 'InboundEmail',
                'jjwg_Address_Cache' => 'jjwg_Address_Cache',
                'jjwg_Areas' => 'jjwg_Areas',
                'jjwg_Maps' => 'jjwg_Maps',
                'jjwg_Markers' => 'jjwg_Markers',
                'LabelEditor' => 'LabelEditor',
                'Leads' => 'Leads',
                'MailMerge' => 'MailMerge',
                'Meetings' => 'Meetings',
                'MergeRecords' => 'MergeRecords',
                'ModuleBuilder' => 'ModuleBuilder',
                'MySettings' => 'MySettings',
                'Notes' => 'Notes',
                'OAuth2Clients' => 'OAuth2Clients',
                'OAuth2Tokens' => 'OAuth2Tokens',
                'OAuthKeys' => 'OAuthKeys',
                'OAuthTokens' => 'OAuthTokens',
                'Opportunities' => 'Opportunities',
                'OptimisticLock' => 'OptimisticLock',
                'OutboundEmailAccounts' => 'OutboundEmailAccounts',
                'Project' => 'Project',
                'ProjectTask' => 'ProjectTask',
                'ProspectLists' => 'ProspectLists',
                'Prospects' => 'Prospects',
                'Relationships' => 'Relationships',
                'Reminders' => 'Reminders',
                'Reminders_Invitees' => 'Reminders_Invitees',
                'ResourceCalendar' => 'ResourceCalendar',
                'Releases' => 'Releases',
                'Roles' => 'Roles',
                'SavedSearch' => 'SavedSearch',
                'Schedulers' => 'Schedulers',
                'SchedulersJobs' => 'SchedulersJobs',
                'SecurityGroups' => 'SecurityGroups',
                'Spots' => 'Spots',
                'Studio' => 'Studio',
                'SugarFeed' => 'SugarFeed',
                'SurveyQuestionOptions' => 'SurveyQuestionOptions',
                'SurveyQuestionResponses' => 'SurveyQuestionResponses',
                'SurveyQuestions' => 'SurveyQuestions',
                'SurveyResponses' => 'SurveyResponses',
                'Surveys' => 'Surveys',
                'Tasks' => 'Tasks',
                'Trackers' => 'Trackers',
                'TemplateSectionLine' => 'TemplateSectionLine',
                'UpgradeWizard' => 'UpgradeWizard',
                'UserPreferences' => 'UserPreferences',
                'Users' => 'Users',
                'vCals' => 'vCals',
                );

        $actual = get_module_dir_list();
        sort($actual);
        sort($expected);
        self::assertSame($expected, $actual);
    }

    public function testremove_file_extension(): void
    {
        //execute the method and test if it returns expected values

        //no file extension
        $expected = '';
        $actual = remove_file_extension('fileNoExt');
        self::assertSame($expected, $actual);

        //simple file extension
        $expected = 'file1';
        $actual = remove_file_extension('file1.txt');
        self::assertSame($expected, $actual);

        //complex filename
        $expected = 'file2.ext1';
        $actual = remove_file_extension('file2.ext1.ext2');
        self::assertSame($expected, $actual);
    }

    public function testwrite_array_to_file(): void
    {
//        $this->markTestSkipped('write_array_to_file cannot be tested with vfsStream');
//        //execute the method and test if it returns true and verify contents
//
//        $cache_dir = 'vfs://root';
//        $tempArray = array('Key1' => array('Key2' => 'value2', 'Key3' => 'value3'));
//
//        //without header
//        $expected = "<?php\n// created: ".date('Y-m-d H:i:s')."\n\$tempArray = array (\n  'Key1' => \n  array (\n    'Key2' => 'value2',\n    'Key3' => 'value3',\n  ),\n);";
//        $actual = write_array_to_file('tempArray', $tempArray, $cache_dir.'\tempArray.txt');
//        $this->assertTrue($actual);
//        $actual_contents = file_get_contents($cache_dir.'\tempArray.txt');
//        $this->assertSame($expected, $actual_contents);
//        unlink($cache_dir.'\tempArray.txt');
//
//        //with header
//        $expected = "test header \$tempArray = array (\n  'Key1' => \n  array (\n    'Key2' => 'value2',\n    'Key3' => 'value3',\n  ),\n);";
//        $actual = write_array_to_file('tempArray', $tempArray, $cache_dir.'\tempArray.txt', 'w', 'test header ');
//        $this->assertTrue($actual);
//        $actual_contents = file_get_contents($cache_dir.'\tempArray.txt');
//        $this->assertSame($expected, $actual_contents);
//        unlink($cache_dir.'\tempArray.txt');
    }

    public function testwrite_encoded_file(): void
    {
        //execute the method and test if it created file exists

        $cache_dir = 'vfs://root';

        //without filename
        $tempArray = array('filename' => 'soap_array.txt', 'md5' => '523ef67de860fc54794f27117dba4fac', 'data' => 'some soap data');
        $actual = write_encoded_file($tempArray, $cache_dir, '');
        self::assertFileExists($actual);
        unlink($actual);

        //with filename
        $tempArray = array('md5' => '523ef67de860fc54794f27117dba4fac', 'data' => 'some soap data');
        $actual = write_encoded_file($tempArray, $cache_dir, 'soap_array.txt');
        self::assertFileExists($actual);
        unlink($actual);
    }

    public function testcreate_custom_directory(): void
    {
        //execute the method and test if it created file/dir exists

        $file = 'Test/';

        $vfs = $this->rootFs;
        if ($vfs->hasChild($file)  == true) {
            rmdir('custom/'.$file);
        }

        $actual = create_custom_directory($file);
        self::assertFileExists($actual);

        if ($vfs->hasChild($file)  == true) {
            rmdir('custom/'.$file);
        }
    }

    public function testmd5DirCompare(): void
    {
        //execute the method and test if it returns expected values

        $expected = array();

        $actual = md5DirCompare('include/MVC/', 'include/MVC/', array('views'));
        self::assertSame($expected, $actual);
    }

    public function testgetFiles(): void
    {
        //execute the method and test if it returns expected values

        //test without pattern
        $expected = array(
            'include/MVC/Controller/ControllerFactory.php',
            'include/MVC/Controller/file_access_control_map.php',
            'include/MVC/Controller/action_file_map.php',
            'include/MVC/Controller/action_view_map.php',
            'include/MVC/Controller/entry_point_registry.php',
            'include/MVC/Controller/SugarController.php',
        );
        $actual = array();
        getFiles($actual, 'include/MVC/Controller');
        sort($actual);
        sort($expected);
        self::assertSame($expected, $actual);

        //test with pattern
        $expected = array(
                'include/MVC/Controller/action_view_map.php',
                'include/MVC/View/views/view.ajax.php',
                'include/MVC/View/views/view.ajaxui.php',
                'include/MVC/View/views/view.classic.config.php',
                'include/MVC/View/views/view.classic.php',
                'include/MVC/View/views/view.config.php',
                'include/MVC/View/views/view.detail.config.php',
                'include/MVC/View/views/view.detail.php',
                'include/MVC/View/views/view.edit.php',
                'include/MVC/View/views/view.favorites.php',
                'include/MVC/View/views/view.html.php',
                'include/MVC/View/views/view.importvcard.php',
                'include/MVC/View/views/view.importvcardsave.php',
                'include/MVC/View/views/view.json.php',
                'include/MVC/View/views/view.list.php',
                'include/MVC/View/views/view.metadata.php',
                'include/MVC/View/views/view.modulelistmenu.php',
                'include/MVC/View/views/view.multiedit.php',
                'include/MVC/View/views/view.noaccess.php',
                'include/MVC/View/views/view.popup.php',
                'include/MVC/View/views/view.quick.php',
                'include/MVC/View/views/view.quickcreate.php',
                'include/MVC/View/views/view.quickedit.php',
                'include/MVC/View/views/view.serialized.php',
                'include/MVC/View/views/view.sugarpdf.config.php',
                'include/MVC/View/views/view.sugarpdf.php',
                'include/MVC/View/views/view.vcard.php',
                'include/MVC/View/views/view.xml.php',
        );
        $actual = array();
        getFiles($actual, 'include/MVC', '@view@');
        sort($expected);
        sort($actual);
        self::assertEquals($expected, $actual);
    }

    public function testreadfile_chunked(): void
    {
        //execute the method and test if it returns expected values

        $expected = file_get_contents('config.php');

        //retbytes parameter false
        ob_start();
        $actual = readfile_chunked('config.php', false);
        $renderedContent = ob_get_contents();
        ob_end_clean();

        self::assertTrue($actual);
        self::assertSame($expected, $renderedContent);

        //retbytes parameter true/default
        ob_start();
        $actual = readfile_chunked('config.php');
        $renderedContent = ob_get_contents();
        ob_end_clean();

        self::assertEquals($actual, strlen($renderedContent));
        self::assertSame($expected, $renderedContent);
    }

    public function testsugar_rename(): void
    {
        //execute the method and test if it returns true/success

        $dir = 'vfs://root';
        $file = 'test.txt';
        $vfs = $this->rootFs;
        if ($vfs->hasChild($file)  != true) {
            write_array_to_file('', '', $dir.'/'.$file);
        }

        //test with empty file names
        $actual = sugar_rename('', '');
        self::assertFalse($actual);

        //test with valid file names
        $actual = sugar_rename($dir.'/'.$file, $dir.'/'.'newtest.txt');
        self::assertTrue($actual);

        unlink($dir.'/'.'newtest.txt');
    }

    public function testfileToHash(): void
    {
        if (isset($_SESSION)) {
            $_session = $_SESSION;
        }

        //execute the method and test if it returns expected values

        //test with empty filename string
        $expected = 'd41d8cd98f00b204e9800998ecf8427e';
        $hash = fileToHash('');
        self::assertSame($expected, $hash);
        self::assertSame('', $_SESSION['file2Hash'][$hash]);

        //test with valid filename
        $expected = '9e5e2527d69c009a81b8ecd730f3957e';
        $hash = fileToHash('config.php');
        self::assertSame($expected, $hash);
        self::assertSame('config.php', $_SESSION['file2Hash'][$hash]);

        if (isset($_session)) {
            $_SESSION = $_session;
        } else {
            unset($_SESSION);
        }
    }

    public function testhashToFile(): void
    {
        if (isset($_SESSION)) {
            $_session = $_SESSION;
        }

        //execute the method and test if it returns expected values

        //test with invalid hash.
        $actual = hashToFile('');
        self::assertFalse($actual);

        //test with a newly generated hash
        $hash = fileToHash('config.php');
        $actual = hashToFile($hash);
        self::assertSame('config.php', $actual);

        if (isset($_session)) {
            $_SESSION = $_session;
        } else {
            unset($_SESSION);
        }
    }

    public function testget_file_extension(): void
    {
        //execute the method and test if it returns expected values

        $file = ''; // Only variables should be passed by reference in php7
        self::assertSame('', get_file_extension($file));

        $file = 'test.txt'; // Only variables should be passed by reference in php7
        self::assertSame('txt', get_file_extension($file));

        $file = 'test.ext.Txt'; // Only variables should be passed by reference in php7
        self::assertSame('Txt', get_file_extension($file, false));

        $file = 'test.ext.TXT'; // Only variables should be passed by reference in php7
        self::assertSame('txt', get_file_extension($file, true));
    }

    public function testget_mime_content_type_from_filename(): void
    {
        //execute the method and test if it returns expected values

        self::assertSame('', get_mime_content_type_from_filename(''));
        self::assertSame('application/octet-stream', get_mime_content_type_from_filename('file.tmp'));
        self::assertSame('text/plain', get_mime_content_type_from_filename('file.txt'));
        self::assertSame('application/x-shockwave-flash', get_mime_content_type_from_filename('file.swf'));
        self::assertSame('video/x-flv', get_mime_content_type_from_filename('file.flv'));
    }

    public function testcleanFileName(): void
    {
        //execute the method and test if it returns expected values

        self::assertSame('file.txt', cleanFileName('file<?>.txt'));
        self::assertSame('file_1.txt', cleanFileName('file_1<?>.txt'));
        self::assertSame('file.txt', cleanFileName('file.txt'));
    }

    public function testcleanDirName(): void
    {
        //execute the method and test if it returns expected values

        self::assertSame('testDir', cleanDirName('./testDir'));
        self::assertSame('testDir', cleanDirName('..\\testDir'));
        self::assertSame('testDir', cleanDirName('\\test/Dir/'));
    }
}
