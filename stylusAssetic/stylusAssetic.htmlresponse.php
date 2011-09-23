<?php
/**
* @package     
* @subpackage  
* @author      Brice Tencé
* @copyright   2011 Brice Tencé
* @link        
* @licence     GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/

/**
* plugin for jResponseHTML, which processes stylus files using assetic
*/

require_once 'Assetic/AssetWriter.php';
require_once 'Assetic/Asset/AssetInterface.php';
require_once 'Assetic/Asset/AssetCollectionInterface.php';
require_once 'Assetic/Asset/Iterator/AssetCollectionIterator.php';
require_once 'Assetic/Asset/Iterator/AssetCollectionFilterIterator.php';
require_once 'Assetic/Asset/AssetCollection.php';
require_once 'Assetic/Filter/FilterInterface.php';
require_once 'Assetic/Filter/FilterCollection.php';
require_once 'Assetic/Asset/BaseAsset.php';
require_once 'Assetic/Asset/FileAsset.php';
require_once 'Assetic/Filter/StylusFilter.php';

require_once 'Assetic/Util/Process.php';
require_once 'Assetic/Util/ProcessBuilder.php';

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Filter\LessFilter;
use Assetic\AssetWriter;
use Assetic\Filter\StylusFilter;


define('STYLUS_ASSETIC_COMPILE_ALWAYS', 1 );
define('STYLUS_ASSETIC_COMPILE_ONCHANGE', 2 ); //default value
define('STYLUS_ASSETIC_COMPILE_ONCE', 3 );

class stylusAsseticHTMLResponsePlugin implements jIHTMLResponsePlugin {

    protected $response = null;

    public function __construct(jResponse $c) {
        $this->response = $c;
    }

    /**
     * called just before the jResponseBasicHtml::doAfterActions() call
     */
    public function afterAction() {
    }

    /**
     * called when the content is generated, and potentially sent, except
     * the body end tag and the html end tags. This method can output
     * directly some contents.
     */
    public function beforeOutput() {
        if (!($this->response instanceof jResponseHtml))
            return;
        global $gJConfig;

        $compileFlag = STYLUS_ASSETIC_COMPILE_ONCHANGE;
        if( isset($gJConfig->jResponseHtml['stylusAssetic_compile']) ) {
            switch($gJConfig->jResponseHtml['stylusAssetic_compile']) {
            case 'always':
                $compileFlag = STYLUS_ASSETIC_COMPILE_ALWAYS;
                break;
            case 'onchange':
                $compileFlag = STYLUS_ASSETIC_COMPILE_ONCHANGE;
                break;
            case 'once':
                $compileFlag = STYLUS_ASSETIC_COMPILE_ONCE;
                break;
            }
        }

        $nodeBinPath = '/usr/bin/node';
        if(isset($gJConfig->jResponseHtml['stylusAssectic_node_bin_path']) && $gJConfig->jResponseHtml['stylusAssectic_node_bin_path'] != '') {
            $nodeBinPath = $gJConfig->jResponseHtml['stylusAssectic_node_bin_path'];
        }

        $nodePaths = array();
        if(isset($gJConfig->jResponseHtml['stylusAssectic_node_paths']) && $gJConfig->jResponseHtml['stylus_node_paths'] != '') {
            $nodePaths = explode(',', $gJConfig->jResponseHtml['stylus_node_paths']);
        }

        $inputCSSLinks = $this->response->getCSSLinks();
        $outputCSSLinks = array();

        foreach( $inputCSSLinks as $inputCSSLinkUrl=>$CSSLinkParams ) {
            $CSSLinkUrl = $inputCSSLinkUrl;
            if( isset($CSSLinkParams['stylus']) ) {
                if( $CSSLinkParams['stylus'] ) {
                    //we suppose url starts with basepath. Other cases should not have a "'stylus' => true" param ...
                    if( substr($CSSLinkUrl, 0, strlen($gJConfig->urlengine['basePath'])) != $gJConfig->urlengine['basePath'] ) {
                        throw new Exception("File $CSSLinkUrl seems not to be located in your basePath : it can not be processed with Assetic's StylusFilter");
                    } else {
                        $filePath = jApp::wwwPath() . substr($CSSLinkUrl, strlen($gJConfig->urlengine['basePath']));

                        $outputSuffix = '';
                        if( substr($filePath, -5) != '.styl' ) {
                            //append .styl at the end of filename if it is not already the case ...
                            $outputSuffix .= '.styl';
                        }
                        $outputSuffix .= '.css';
                        $outputPath = $filePath.$outputSuffix;

                        $compile = true;
                        if( is_file($outputPath) ) {
                            if( ($compileFlag == STYLUS_ASSETIC_COMPILE_ALWAYS) ) {
                                unlink($outputPath);
                            } elseif( ($compileFlag == STYLUS_ASSETIC_COMPILE_ONCE) ) {
                                $compile = false;
                            } elseif( ($compileFlag == STYLUS_ASSETIC_COMPILE_ONCHANGE) && filemtime($filePath) <= filemtime($outputPath) ) {
                                $compile = false;
                            }
                        }
                        if( $compile ) {
                            $css = new AssetCollection(array(
                                new FileAsset($filePath, array(new StylusFilter($nodeBinPath, $nodePaths)))
                            ));

                            file_put_contents( $outputPath, $css->dump() );
                        }
                        $CSSLinkUrl = $CSSLinkUrl . $outputSuffix;
                    }
                }
                unset($CSSLinkParams['stylus']);
            }

            $outputCSSLinks[$CSSLinkUrl] = $CSSLinkParams;
        }

        $this->response->setCSSLinks( $outputCSSLinks );
    }

    /**
     * called just before the output of an error page
     */
    public function atBottom() {
    }

    /**
     * called just before the output of an error page
     */
    public function beforeOutputError() {
    }
}
