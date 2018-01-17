<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Console;

use SebastianFeldmann\CaptainHook\CH;
use Symfony\Component\Console\Application as SymfonyApplication;

/**
 * Class Application
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class Application extends SymfonyApplication
{
    /**
     * @var \SebastianFeldmann\CaptainHook\Config
     */
    protected $config;

    /**
     * @var string
     */
    private static $logo = '   .oooooo.                            .              o8o              
  d8P\'  `Y8b                         .o8              `"\'              
 888           .oooo.   oo.ooooo.  .o888oo  .oooo.   oooo  ooo. .oo.   
 888          `P  )88b   888\' `88b   888   `P  )88b  `888  `888P"Y88b  
 888           .oP"888   888   888   888    .oP"888   888   888   888  
 `88b    ooo  d8(  888   888   888   888 . d8(  888   888   888   888  
  `Y8bood8P\'  `Y888""8o  888bod8P\'   "888" `Y888""8o o888o o888o o888o 
                         888                                           
                        o888o
      
                         .ed"""" """$$$$be.                     
                       -"           ^""**$$$e.                  
                     ."                   \'$$$c                 
                    /                      "4$$b                
                   d  3                     $$$$                
                   $  *                   .$$$$$$               
                  .$  ^c           $$$$$e$$$$$$$$.              
                  d$L  4.         4$$$$$$$$$$$$$$b              
                  $$$$b ^ceeeee.  4$$ECL.F*$$$$$$$              
      e$""=.      $$$$P d$$$$F $ $$$$$$$$$- $$$$$$              
     z$$b. ^c     3$$$F "$$$$b   $"$$$$$$$  $$$$*"      .=""$c  
    4$$$$L   \     $$P"  "$$b   .$ $$$$$...e$$        .=  e$$$. 
    ^*$$$$$c  %..   *c    ..    $$ 3$$$$$$$$$$eF     zP  d$$$$$ 
      "**$$$ec   "\   %ce""    $$$  $$$$$$$$$$*    .r" =$$$$P"" 
            "*$b.  "c  *$e.    *** d$$$$$"L$$    .d"  e$$***"   
              ^*$$c ^$c $$$      4J$$$$$% $$$ .e*".eeP"         
                 "$$$$$$"\'$=e....$*$$**$cz$$" "..d$*"           
                   "*$$$  *=%4.$ L L$ P3$$$F $$$P"              
                      "$   "%*ebJLzb$e$$$$$b $P"                
                        %..      4$$$$$$$$$$ "                  
                         $$$e   z$$$$$$$$$$%                    
                          "*$c  "$$$$$$$P"                      
                           ."""*$$$$$$$$bc                      
                        .-"    .$***$$$"""*e.                   
                     .-"    .e$"     "*$c  ^*b.                 
              .=*""""    .e$*"          "*bc  "*$e..            
            .$"        .z*"               ^*$e.   "*****e.      
            $$ee$c   .d"                     "*$.        3.     
            ^*$E")$..$"                         *   .ee==d%     
               $.d$$$*                           *  J$$$e*      
                """""                             "$$$"

             ooooo   ooooo                     oooo        
             `888\'   `888\'                     `888        
              888     888   .ooooo.   .ooooo.   888  oooo  
              888ooooo888  d88\' `88b d88\' `88b  888 .8P\'   
              888     888  888   888 888   888  888888.    
              888     888  888   888 888   888  888 `88b.  
             o888o   o888o `Y8bod8P\' `Y8bod8P\' o888o o888o 

';

    /**
     * Input output interface
     *
     * @var \SebastianFeldmann\CaptainHook\Console\IO
     */
    protected $io;

    /**
     * Application constructor.
     */
    public function __construct()
    {
        if (function_exists('ini_set') && extension_loaded('xdebug')) {
            ini_set('xdebug.show_exception_trace', false);
            ini_set('xdebug.scream', false);
        }
        parent::__construct('CaptainHook', CH::VERSION);

        $this->setDefaultCommand('help');
    }

    /**
     * Append release date to version output.
     *
     * @return string
     */
    public function getLongVersion()
    {
        return sprintf(
            '<info>%s</info> version <comment>%s</comment> %s',
            $this->getName(),
            $this->getVersion(),
            CH::RELEASE_DATE
        );
    }

    /**
     * Prepend help with logo.
     *
     * @return string
     */
    public function getHelp()
    {
        return self::$logo . parent::getHelp();
    }
}
