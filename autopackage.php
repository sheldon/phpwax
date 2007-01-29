<?php
/**
 * package.xml generation file for patTemplate
 *
 * This file is executed by createSnaps.php to
 * automatically create a package that can be
 * installed via the PEAR installer.
 *
 * $Id$
 *
 * @author		Stephan Schmidt <schst@php-tools.net>
 */


/**
 * uses PackageFileManager
 */
require_once 'PEAR/PackageFileManager2.php';
require_once 'PEAR/PackageFileManager/Svn.php';

/**
 * Base version
 */
$base_version = '0.7.1';

/**
 * current version
 */
$version	= $baseVersion;
$dir		= dirname( __FILE__ );


/**
 * current state
 */
$state = 'alpha';

/**
 * release notes
 */
$notes = <<<EOT
See dev.php-wax.com for details
EOT;

/**
 * package description
 */
$description = <<<EOT
Pear install of the PHP-WAX framework
EOT;

$package = new PEAR_PackageFileManager2();

$result = $package->setOptions(array(
    'license'           => 'MIT',
    'filelistgenerator' => 'file',
    'ignore'            => array( 'package.php', 'autopackage2.php', 'package.xml', '.cvsignore', '.svn', 'examples/cache', 'rfcs' ),
    'simpleoutput'      => true,
    'baseinstalldir'    => 'phpwax',
    'packagedirectory'  => './',
    'dir_roles'         => array(
								'system' => 'script'
                                 )
    ));
if (PEAR::isError($result)) {
    echo $result->getMessage();
    die();
}

$package->setPackage('phpwax');
$package->setSummary('Full Stack PHP Framework');
$package->setDescription($description);

$package->setChannel('pear.php-wax.com');
$package->setReleaseVersion($version);
$package->setReleaseStability($state);
$package->setNotes($notes);
$package->setPackageType('php');
$package->setLicense('MIT', 'http://www.opensource.org/licenses/mit-license.php');

$package->addMaintainer('lead', 'phpwax', 'PHP-WAX', 'riley.ross@gmail.com', 'yes');

$package->setPhpDep('5.1.0');
$package->setPearinstallerDep('1.4.0');
$package->generateContents();

$result = $package->writePackageFile();

if (PEAR::isError($result)) {
    echo $result->getMessage();
    die();
}
?>
