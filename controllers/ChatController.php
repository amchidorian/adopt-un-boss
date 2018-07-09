<?php

namespace BWB\Framework\mvc\controllers;

use BWB\Framework\mvc\Controller;
use BWB\Framework\mvc\dao\DAOChat;
use BWB\Framework\mvc\dao\DAOMatch;
use BWB\Framework\mvc\dao\DAOOffre;
use BWB\Framework\mvc\dao\DAOUser;
use BWB\Framework\mvc\SecurityMiddleware;
use BWB\Framework\mvc\controllers\SecurityController;

class ChatController extends Controller {

    private $dao_match;
    private $dao_user;
    private $dao_offre;
    private $dao_chat;

    function __construct() {
        parent::__construct();
        $this->dao_match = new DAOMatch();
        $this->dao_user = new DAOUser();
        $this->dao_offre = new DAOOffre();
        $this->dao_chat = new DAOChat();
        $this->security_middleware = new SecurityMiddleware();
        $this->security_controller = new SecurityController();
    }

   

    /**
     * Méthode qui retourne la vue.
     */
    public function get_view() {
        $this->render("chat");
    }

    /**
     * Méthode qui recherche les utilisateurs a qui l'utilisateur connecté peut 
     * envoyé un message. Il faut qu'il y ai un match.
     * 
     * @param type $id
     */
    public function get_users($id_user) {
        $permission_user = $this->dao_user->get_user_permission($id_user);
        $permission_recepteur = ($permission_user == "entreprise" ? "candidat" : "entreprise");
        $matchs = $this->dao_match->get_match($id_user, $permission_user, $permission_recepteur);
        return $this->get_and_order_user($matchs, $id_user, $permission_recepteur);
    }

    /**
     * Méthode qui transforme les entité récupéré dans les match en objet.
     * 
     * @param type $matchs
     * @param type $permission_recepteur
     * @return array
     */
    protected function get_and_order_user($matchs, $id_user, $permission_recepteur) {
        $users = array();
        foreach ($matchs as $match):
            $user = $this->dao_chat->get_date_and_last_message($id_user, $match[0]);
            array_push($users, array(
                "recepteur" => $this->dao_user->retrieve_user($permission_recepteur, $match[0])->to_array(),
                "timestamp" => $user['MAX(date_creation)'],
                "message" => $user['message']
            ));
        endforeach;
        return $this->order_users($users);
    }

    protected function order_users($users) {
        $timestamp = array();
        $temp = array();
        foreach ($users as $user):
            array_push($timestamp, $user['timestamp']);
        endforeach;
        $timestamps = $this->order_timestamp($timestamp);
        $longueur = count($timestamps);
        for ($i = 0; $i < $longueur; $i++):
            foreach ($users as $user):
                if ($timestamps[$i] == $user['timestamp']):
                    array_push($temp, $user);
                endif;
            endforeach;
        endfor;
        return $temp;
    }

    protected function order_timestamp($timestamps) {
        $longueur = count($timestamps);
        for ($j = 0; $j < $longueur; $j++):
            for ($k = $longueur - 1; $k > $j; $k--):
                if ($timestamps[$j] < $timestamps[$k]):
                    $tempvalue = $timestamps[$j];
                    $timestamps[$j] = $timestamps[$k];
                    $timestamps[$k] = $tempvalue;
                endif;
            endfor;
        endfor;
        return $timestamps;
    }

    public function get_messages($id_user, $id_recepteur) {
        $results = $this->dao_chat->get_all_messages($id_user, $id_recepteur);
        $messages = array();
        foreach ($results as $result):
            $result_array = $result->to_array();
            array_push($messages, array($result_array['emetteur_id'] => $result_array));
        endforeach;
        return $messages;
    }

    public function save_message($id_emetteur, $id_recepteur, $msg) {
        $timestamp = time();
        $this->dao_chat->save_message($id_emetteur, $id_recepteur, $msg, $timestamp);
    }

}
