<?php 

namespace Hexters\Ladmin\Helpers;

use Exception;
use Hexters\Ladmin\Exceptions\LadminException;
use Hexters\Ladmin\Models\LadminNotification;
use Hexters\Ladmin\Events\LadminNotificationEvent;

class Notification {

  private $title;
  private $link;
  private $image_link;
  private $description;
  private $gates = [];
  private $menu;

  public function __construct() {
    $this->menu = new Menu;
  }

  public function setTitle($title) {
    $this->title = $title;
    return $this;
  }

  public function setLink($link) {
    $this->link = $link;
    return $this;
  }

  public function setImageLink($image_link) {
    $this->image_link = $image_link;
    return $this;
  }

  public function setDescription($description) {
    $this->description = $description;
    return $this;
  }

  public function setGates($gates) {

    if(is_array($gates)) {
      $this->gates = $gates;
    } else if( is_string($gates) ) {
      $this->gates = [$gates];
    }

    return $this;
  }

  public function send() {
    try {
      if(empty($this->title)) {
        throw new Exception('Title is required');
      }
      if(empty($this->link)) {
        throw new Exception('Link is required');
      }
      if(empty($this->description)) {
        throw new Exception('Description is required');
      }

      if(is_null($this->gates) || count($this->gates) < 1 || empty($this->gates)) {
        $gates = $this->menu->gates($this->menu->menus);
        if( is_array($gates) ) {
          $this->gates = $gates;
        }
      }
      
      $notification = LadminNotification::create([
        'title' => $this->title,
        'link' => $this->link,
        'image_link' => $this->image_link,
        'description' => $this->description,
        'gates' => $this->gates,
      ]);
      
      event(new LadminNotificationEvent($notification));

      return [
        'result' => true
      ];
    } catch(Exception $ex) {
      return [
        'result' => false,
        'message' => $ex->getMessage()
      ];
    } catch (LadminException $e) {}
  }


}