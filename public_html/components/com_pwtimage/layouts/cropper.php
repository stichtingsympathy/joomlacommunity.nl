<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2020 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Uri\Uri;
use Pwtimage\Filesystem\Folder;
use Pwtimage\Pwtimage;

/**
 * This is the entry point for the modal to show PWT Image
 */

// Load the component language file
$language = Factory::getLanguage();
$language->load('com_pwtimage', JPATH_ADMINISTRATOR . '/components/com_pwtimage');

// Load the JavaScript and CSS files
HTMLHelper::_('script', 'com_pwtimage/pwtimage.min.js', ['relative' => true, 'version' => 'auto']);
HTMLHelper::_('stylesheet', 'com_pwtimage/pwtimage.min.css', ['relative' => true, 'version' => 'auto']);

// Set the JavaScript language strings
Text::script('COM_PWTIMAGE_IMAGE_LOADING', true);
Text::script('COM_PWTIMAGE_CHOOSE_IMAGE', true);
Text::script('COM_PWTIMAGE_SAVE_FAILED', true);
Text::script('COM_PWTIMAGE_ERROR_ALLOWED_MEMORY_SIZE', true);

// Get our helper
if (!class_exists('PwtImageHelper'))
{
	require_once JPATH_ADMINISTRATOR . '/components/com_pwtimage/helpers/pwtimage.php';
}

$pwtImage    = new Pwtimage;
$folderClass = new Folder;
$input       = Factory::getApplication()->input;

// Set the default values
$modalId              = $input->getCmd('modalId', uniqid());
$ratio                = [];
$freeRatio            = true;
$useOriginal          = true;
$keepOriginal         = true;
$width                = null;
$sourcePath           = '/images';
$subPath              = '{year}/{month}';
$showUpload           = true;
$showFolder           = true;
$showSavePath         = true;
$showSavePathSelect   = true;
$toCanvas             = false;
$showRotationTools    = true;
$showAspectRatioTools = true;
$showFlippingTools    = true;
$showImageInfo        = true;
$showZoomTools        = true;
$activePage           = 'upload';
$imagePreview         = '';
$multiple             = false;
$repeatable           = false;
$showHelp             = true;
$baseFolder           = $folderClass->getImageFolder(true);
$imageFolder          = $folderClass->getImageFolder();
$maxSize              = (string) $pwtImage->fileUploadMaxSize();
$maxSizeMessage       = Text::_('COM_PWTIMAGE_MAX_SIZE_MESSAGE');
$maxDimension         = 15000;
$buttonText           = 'JSELECT';
$wysiwyg              = $input->getBool('wysiwyg', false);
$siteUrl              = Uri::current();
$origin               = isset($displayData['origin']) ? $displayData['origin'] : '';
$viewMode             = 1;
$backgroundColor      = $pwtImage->getSetting('backgroundColor', '#000000');
$disableDefaultFolder = $pwtImage->getSetting('disableDefaultFolder', 0);

// Get the settings passed from the button
$settings = json_decode(base64_decode($input->getBase64('settings')), true);

if (is_array($settings) && array_key_exists('origin', $settings))
{
	$origin = $settings['origin'];
}

// Get the settings from the profile
if (!class_exists('PwtimageHelper'))
{
	require_once JPATH_ADMINISTRATOR . '/components/com_pwtimage/helpers/pwtimage.php';
}

$pwtImage = new Pwtimage($origin);
extract($pwtImage->getSettings(), EXTR_OVERWRITE);

// Get the settings from the XML file
/**
 * @param   int     $modalId               The unique ID for the modal
 * @param   string  $ratio                 The image ratio to use
 * @param   bool    $freeRatio             Set if the free ratio should be shown
 * @param   bool    $useOriginal           Use the original image
 * @param   bool    $keepOriginal          Set if the user should get the option to keep the original image size
 * @param   int     $width                 The fixed with for an image
 * @param   string  $sourcePath            The main image path
 * @param   string  $subPath               The image sub-folder
 * @param   bool    $showUpload            Set if the image upload should be shown
 * @param   bool    $showFolder            Set if the image selection from server should be shown
 * @param   bool    $showSavePath          Set if the save path needs to be shown
 * @param   bool    $showSavePathSelect    Set if the select option of the save path needs to be shown
 * @param   bool    $toCanvas              Set if the image is shown directly on the canvas for editing
 * @param   bool    $showRotationTools     Set if the rotation tools needs to be shown
 * @param   bool    $showAspectRatioTools  Set if the aspect ratio tools needs to be shown
 * @param   bool    $showFlippingTools     Set if the flipping tools needs to be shown
 * @param   bool    $showImageInfo         Set if the image info must be hidden
 * @param   bool    $showZoomTools         Set if the zoom tools needs to be shown
 * @param   string  $activePage            Set which tab should be shown by default this is the upload tab
 * @param   string  $imagePreview          A given image to show in preview
 * @param   bool    $multiple              Set if multiple images should be allowed
 * @param   bool    $repeatable            Set if images are in a subform
 * @param   bool    $showHelp              Set if the help option should be shown
 * @param   int     $viewMode              Set the cropping mode
 * @param   string  $backgroundColor       Set the background color to use for cropping
 * @param   string  $disableDefaultFolder  Hide default option
 */
/** @var array $displayData */
extract($displayData, EXTR_OVERWRITE);

// Extract the settings passed to the iFrame, overriding the profile settings
if (is_array($settings))
{
	// Remove empty values, the profile values will be used
	foreach ($settings as $key => $setting)
	{
		if ($setting !== '')
		{
			$$key = $setting;
		}
	}
}

if (!$showUpload && !$showFolder)
{
	Factory::getApplication()->enqueueMessage(Text::_('COM_PWTIMAGE_NO_UPLOAD_NO_FOLDER'), 'error');
}

// Sanity check on the source path, it cannot be empty
if (!$sourcePath)
{
	$sourcePath = '/images';
}

// Transform fields that accept multiple values
if ($ratio && is_string($ratio))
{
	$ratios       = [];
	$stringRatios = explode('|', $ratio);

	foreach ($stringRatios as $index => $stringRatio)
	{
		$ratio           = [];
		$ratio['width']  = 'NaN';
		$ratio['height'] = 'NaN';

		if (strpos($stringRatio, '/') !== false)
		{
			list($ratio['width'], $ratio['height']) = explode('/', $stringRatio);
		}

		$ratios[] = $ratio;
	}

	// Set the new values
	$ratio = $ratios;
}

if ($width && is_string($width))
{
	$widths       = [];
	$stringWidths = explode('|', $width);

	foreach ($stringWidths as $index => $stringWidth)
	{
		$widths[] = ['width' => $stringWidth];
	}

	// Set the new values
	$width = $widths;
}

// Enforce the modalId from the URL when multiple is enabled
if ($multiple || $repeatable)
{
	$modalId = $input->getCmd('modalId', uniqid());
}

// Enrich the displayData for the sublayouts
$displayData['modalId']              = $modalId;
$displayData['ratio']                = $ratio;
$displayData['freeRatio']            = $freeRatio;
$displayData['useOriginal']          = filter_var($useOriginal, FILTER_VALIDATE_BOOLEAN);
$displayData['keepOriginal']         = filter_var($keepOriginal, FILTER_VALIDATE_BOOLEAN);
$displayData['width']                = $width;
$displayData['sourcePath']           = $sourcePath;
$displayData['subPath']              = $subPath;
$displayData['showUpload']           = filter_var($showUpload, FILTER_VALIDATE_BOOLEAN);
$displayData['showFolder']           = filter_var($showFolder, FILTER_VALIDATE_BOOLEAN);
$displayData['showSavePath']         = filter_var($showSavePath, FILTER_VALIDATE_BOOLEAN);
$displayData['showSavePathSelect']   = filter_var($showSavePathSelect, FILTER_VALIDATE_BOOLEAN);
$displayData['toCanvas']             = filter_var($toCanvas, FILTER_VALIDATE_BOOLEAN);
$displayData['showRotationTools']    = $showRotationTools;
$displayData['showAspectRatioTools'] = filter_var($showAspectRatioTools, FILTER_VALIDATE_BOOLEAN);
$displayData['showFlippingTools']    = filter_var($showFlippingTools, FILTER_VALIDATE_BOOLEAN);
$displayData['showImageInfo']        = filter_var($showImageInfo, FILTER_VALIDATE_BOOLEAN);
$displayData['showZoomTools']        = filter_var($showZoomTools, FILTER_VALIDATE_BOOLEAN);
$displayData['maxSize']              = $maxSize;
$displayData['maxSizeMessage']       = $maxSizeMessage;
$displayData['maxDimension']         = $maxDimension;
$displayData['wysiwyg']              = filter_var($wysiwyg, FILTER_VALIDATE_BOOLEAN);
$displayData['canDo']                = $canDo;
$displayData['disableDefaultFolder'] = $disableDefaultFolder;

// Get the first ratio
$setRatio = '1/1';

// Fix the canDo to be a CMSObject again
if ($canDo instanceof CMSObject === false)
{
	$canDo = new CMSObject($canDo);
}

if ($displayData['ratio'])
{
	if (is_array($displayData['ratio']) && count($displayData['ratio']) > 0)
	{
		$element  = reset($displayData['ratio']);
		$setRatio = $element['width'] . '/' . $element['height'];
	}
	else
	{
		$setRatio = $displayData['ratio'];
	}
}

// Set which tab should be visible
$uploadActive = 'is-active';
$folderActive = '';

if ((!$showUpload || !$canDo->get('pwtimage.accessupload')) || (($showFolder && !$canDo->get('pwtimage.accessfolder')) && $activePage === 'folder'))
{
	$uploadActive = '';
	$folderActive = 'is-active';
}

$iFrameLink  = Uri::root() . 'index.php?option=com_pwtimage&amp;view=image&layout=iframe&amp;tmpl=component';
$rootPath    = Uri::root();
$params      = ComponentHelper::getParams('com_pwtimage');
$imagePrefix = $params->get('prefix', '');
$imageSuffix = $params->get('suffix', '');

// Do some sanity checks
$user = Factory::getUser();

Factory::getDocument()->addScriptDeclaration(<<<JS
	jQuery(document).ready(function (){
	    // Make sure we are in an iFrame
	    if (window.self === window.top)
        {
			window.location = '{$siteUrl}';   
        }
        pwtImage.initialise();
		pwtImage.setIframeLink('{$iFrameLink}');
		pwtImage.setWysiwyg('{$wysiwyg}');
		pwtImage.setTargetId('{$modalId}');
		pwtImage.setRootPath('{$rootPath}');
		pwtImage.setImagePrefix('{$imagePrefix}');
		pwtImage.setImageSuffix('{$imageSuffix}');
		pwtImage.setViewMode('{$viewMode}');
		
		// New tabs
		jQuery('[data-tab]').on('click', function(event) {
			var tabId = jQuery(this).attr('href');
			jQuery('[data-tab]').parent().removeClass('is-active');
			jQuery(this).parent().addClass('is-active');
			jQuery('[data-tabs-pane]').removeClass('is-active');
			jQuery(tabId).addClass('is-active');
			event.preventDefault();
		});
	
		// Initiate tabscroller
		vanillaTabScroller.init({
			wrapper: '[data-tabs-wrapper-{$modalId}]'
		});
		
		// Set the button visibility
		jQuery('.js-button-image').prop('disabled', true);
		
		jQuery('[href="#upload"], [href="#select"], [href="#help"]').on('click', function() {
		    // Only disable the button if there is no image on the canvas
		    if (document.getElementById('{$modalId}_preview').value === '')
		    {
				jQuery('.js-button-image').prop('disabled', true);
			}
		});
	});
JS
);
?>

<script>document.body.classList.add('pwt-image-styling');</script>

<!-- ID field is required here so the script knows which block it must target -->
<div class="pwt-component" id="<?php echo $modalId; ?>_modal">

	<!-- PWT Id container -->
	<div class="pwt-id js-pwtimage-id" id="<?php echo $modalId; ?>">

		<!-- Header -->
		<div class="pwt-header">

			<!-- Tabs -->
			<div class="pwt-tabs-wrapper" data-tabs-wrapper-<?php echo $modalId; ?>>
				<div class="pwt-tabs-scroller" data-tabs-scroller>
					<ul class="pwt-tabs" data-tabs>
						<?php if ($showUpload && $canDo->get('pwtimage.accessupload'))
							:
							?>
							<li class="<?php echo $uploadActive; ?>">
								<a data-tab href="#upload"><?php echo Text::_('COM_PWTIMAGE_TAB_UPLOAD'); ?></a>
							</li>
						<?php endif; ?>
						<?php
						if ($showFolder && $canDo->get('pwtimage.accessfolder'))
							:
							?>
							<li class="<?php echo $folderActive; ?>">
								<a data-tab href="#select"><?php echo Text::_('COM_PWTIMAGE_TAB_SELECT'); ?></a>
							</li>
						<?php endif; ?>
						<?php
						if ($showUpload || $showFolder)
							:
							?>
							<li>
								<a data-tab href="#edit"><?php echo Text::_('COM_PWTIMAGE_TAB_EDIT'); ?></a>
							</li>
						<?php endif; ?>
						<?php
						if ($showHelp)
							:
							?>
							<li>
								<a data-tab href="#help"><?php echo Text::_('COM_PWTIMAGE_TAB_HELP'); ?></a>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			</div><!-- .pwt-tabs-wrapper -->

		</div><!-- .pwt-header -->

		<!-- Body -->
		<div class="pwt-body">

			<!-- Tabs panes -->
			<div class="pwt-tabs-panes" data-tabs-content>
				<?php if ($showUpload && $canDo->get('pwtimage.accessupload'))
					:
					?>
					<div class="pwt-tabs-pane <?php echo $uploadActive; ?>" data-tabs-pane id="upload">
						<?php echo $this->sublayout('upload', $displayData); ?>
					</div>
				<?php endif; ?>
				<?php
				if ($showFolder && $canDo->get('pwtimage.accessfolder'))
					:
					?>
					<div class="pwt-tabs-pane <?php echo $folderActive; ?>" data-tabs-pane id="select">
						<?php echo $this->sublayout('select', ['baseFolder' => $baseFolder, 'sourcePath' => $sourcePath, 'wysiwyg' => $wysiwyg]); ?>
					</div>
				<?php endif; ?>
				<?php
				if (($showUpload && $canDo->get('pwtimage.accessupload')) || ($showFolder && $canDo->get('pwtimage.accessfolder')))
					:
					?>
					<div class="pwt-tabs-pane" data-tabs-pane id="edit">
						<?php echo $this->sublayout('edit', $displayData); ?>
					</div>
				<?php endif; ?>
				<?php
				if ($showHelp)
					:
					?>
					<div class="pwt-tabs-pane" data-tabs-pane id="help">
						<?php echo $this->sublayout('help', []); ?>
					</div>
				<?php endif; ?>
			</div><!-- .pwt-tabs-panes -->

		</div><!-- .pwt-body -->

		<!-- Footer -->
		<div class="pwt-footer">
			<button class="pwt-button pwt-button--success process-image js-button-image"
			        onclick="return pwtImage.saveImage('<?php echo $modalId; ?>');">
				<?php echo Text::_('COM_PWTIMAGE_INSERT_IMAGE'); ?>
			</button>
			<button class="pwt-button" type="button" onclick="pwtImage.closeModal();">
				<?php echo Text::_('COM_PWTIMAGE_CLOSE_MODAL') ?>
			</button>
		</div><!-- .pwt-footer -->

		<!-- Input fields -->
		<input type="hidden" id="post-url"
		       value="//<?php echo Uri::getInstance()->toString(['host', 'path']); ?>"/>
		<input type="hidden" class="js-pwt-image-data" name="pwt-image-data">
		<input type="hidden" class="js-pwt-image-ratio" name="pwt-image-ratio" value="<?php echo $setRatio; ?>">
		<input type="hidden" class="js-pwt-image-sourcePath" name="pwt-image-sourcePath"
		       value="<?php echo $sourcePath; ?>">
		<input type="hidden" class="js-pwt-image-maxsize" name="pwt-image-maxsize" value="<?php echo $maxSize; ?>">
		<input type="hidden" class="js-pwt-image-maxsize-message" name="pwt-image-maxsize-message"
		       value="<?php echo $maxSizeMessage; ?>">
		<input type="hidden" class="js-pwt-image-dimensionsize" name="pwt-image-dimensionsize"
		       value="<?php echo $maxDimension; ?>">
		<input type="hidden" class="js-pwt-image-localfile" name="pwt-image-localFile" value="">
		<input type="hidden" class="js-pwt-image-origin" name="pwt-image-origin" value="<?php echo $origin; ?>">
		<input type="hidden" class="js-pwt-image-backgroundColor" name="pwt-image-backgroundColor"
		       value="<?php echo $backgroundColor; ?>">

	</div><!-- .pwt-id -->

</div><!-- .pwt-component -->
