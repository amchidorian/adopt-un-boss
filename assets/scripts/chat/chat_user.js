$(document).ready(function () {
    get_users();
});

function get_users() {
    $.ajax({
        type: "GET",
        url: "http://adopt-un-boss.bwb/api/chat",
        success: function (data) {
            console.log(data);
            create_user_card(data);
        },
        error: function () {
            console.log("error");
        }
    });
}

function affichage_messages(id) {
    $( ".pastille"+id ).remove();
    $(".chat_list").removeClass("active_chat")
    $.ajax({
        type: "GET",
        url: "http://adopt-un-boss.bwb/api/chat/" + id,
        success: function (data) {
            creation_chat(data, id);
        },
        error: function () {
            console.log("error");
        }
    });
    $("#" + id).addClass("active_chat");
    $('.msg_history').scrollTop($('.msg_history').prop("scrollHeight"));
}

function save_message(id) {
    var data = {
        "msg": $(".write_msg").val()
    }
    $.ajax({
        type: "POST",
        url: "http://adopt-un-boss.bwb/api/chat/" + id,
        dataType: "json",
        data: data,
        success: function () {
            console.log('ok');
        },
        error: function () {
            console.log("error");
        }
    });
    get_users();
    affichage_messages(id);
}

function get_cookie_user() {
    $.ajax({
        type: "GET",
        url: "http://adopt-un-boss.bwb/api/cookie/user",
        success: function (data) {
            pastille(data);
        },
        error: function () {
            console.log("error");
        }
    });
}

function create_user_card(data) {
    $(".inbox_chat").empty();
    for (var i = 0; i < data.length; i++) {
        var id = data[i]['recepteur']['user_id'];
        var timestamp = timestamp_to_date(data[i]['timestamp']);
        $(".inbox_chat").append(
                $("<div>").addClass('chat_list').attr('id', id).attr('onclick', 'affichage_messages(' + id + ')').append(
                $("<div>").addClass('chat_people').append(
                $("<div>").addClass('chat_img').append(
                $("<img>").attr('src', data[i]['recepteur']['logo']))).append(
                $("<div>").addClass('chat_ib').append(
                $("<h5>").addClass('user_name').text(data[i]['recepteur']['nom']))).append(
                $("<p>").addClass('user_message').text(data[i]['message'])).append(
                $("<h5>").addClass('user_date').text(timestamp))));
        if (i === 0) {
            affichage_messages(id);
        }
    }
        get_cookie_user();
}

function pastille(data) {
    for (var i = 0; i < data.length; i++) {
        console.log(data[i])
        $("#" + data[i]).append(
                $("<img>").addClass('pastille'+data[i]).attr('src', "/assets/imgs/pastille.png"));
    }
}

function timestamp_to_date($timestamp) {
    var actual = Math.round(new Date().getTime() / 1000);
    var last_message = actual - $timestamp;
    if (last_message >= 604800) {
        return "il y a plus d'une semaine";
    } else if (604800 > last_message && last_message >= 86400) {
        var jour = last_message / 86400;
        jour = Math.floor(jour);
        if (jour === 1) {
            return  'il y a plus de ' + jour + ' jour';
        } else {
            return  'il y a plus de ' + jour + ' jours';
        }
    } else if (86400 > last_message && last_message >= 3600) {
        var heure = last_message / 3600;
        heure = Math.floor(heure);
        if (heure === 1) {
            return  'il y a ' + heure + ' heure';
        } else {
            return  'il y a plus de ' + heure + 'heures';
        }
    } else {
        var minute = last_message / 3600;
        minute = Math.floor(minute);
        if (minute === 0) {
            return  "il y a moins d'une minute";
        } else if (minute === 1) {
            return  'il y a ' + minute + ' minute';
        } else {
            return  'il y a plus de ' + minute + ' minutes';
        }
    }
}

function creation_chat(data, id) {
    $(".msg_history").empty();
    $(".input_msg_write").append(
            $("<button>").addClass('msg_send_btn').attr('type', 'button')
            .attr('onclick', 'save_message(' + id + ')').text('S'));
    for (var i = 0;
            i < data.length;
            i++
            ) {
        key = Object.keys(data[i]);
        console.log(data[i]);
        console.log(data[i][key]['contenu']);
        if (id == key) {
            $(".msg_history").append(
                    $("<div>").addClass('incoming_msg').append(
                    $("<div>").addClass('incoming_msg_img').append(
                    $("<img>").attr('src', '')).append(
                    $("<div>").addClass('received_msg').append(
                    $("<div>").addClass('received_withd_msg').append(
                    $("<p>").text(data[i][key]['contenu'])).append(
                    $("<span>").addClass('time_date').text('25 Jui 11h10'))))));
        } else {
            $(".msg_history").append(
                    $("<div>").addClass('outgoing_msg').append(
                    $("<div>").addClass('sent_msg').append(
                    $("<p>").text(data[i][key]['contenu'])).append(
                    $("<span>").addClass('time_date').text('25 Jui 11h10'))));
        }

    }
}