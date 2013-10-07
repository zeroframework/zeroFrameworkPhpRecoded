<?php
/**
 * Created by IntelliJ IDEA.
 * User: Gauthier
 * Date: 05/05/13
 * Time: 16:35
 * To change this template use File | Settings | File Templates.
 */

  namespace bridge;

  // Vu qu'on ai pas le namespace racine le \ pour spécifié la raicine
  // On associe la classe event à l'alias eventManager
  // Pour la raison que le trait s'apelle pareil et donc éviter la confusion l'ors de la lecture
  use \event as eventManager;

  /**
   * Class event
   * @package bridge
   * Pont entre le core et le gestionaire d'evenement
   */
  trait event {

      // Instance du gestion d'événement
      private $eventManager;

      // On passe le gestionaire d'evenement
      private function setEventManager($eventManager)
      {
            $this->eventManager = $eventManager;
      }

      // Retourne l'instance de l'eventManager
      public function getEventManager()
      {
            if($this->eventManager === null) $this->setEventManager(new eventManager());

            return $this->eventManager;
      }
  }