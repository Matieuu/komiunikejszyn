String.prototype.equalsIgnoreCase = function (compareString) { return this.toUpperCase() === compareString.toUpperCase(); };

var count = 25;
var generatingMess = false;

const mess = document.getElementById('mess');
const text = document.getElementById('text');

console.log(`id: ${userid}; mess: ${messid}; count: ${count}`);

function redirect(messid) {
    window.location.replace(`./index.php?id=${userid}&mess=${messid}&count=${count}`);
    this.messid = messid;
}

function back() {
    window.location.replace(`./index.php?id=${this.userid}`);
    this.messid = 0;
    this.count = 25;
}

function add() {
    let user = prompt("Wpisz ID użytkownika, którego chcesz dodać", '');
    if (user && user !== '') fetch("../backend/ajax.php?id="+ userid +"&mess="+ messid +"&action=add&add="+ user).then();
}

function create() {
    let groupname = prompt("wpisz nazwę grupy", "");
    let user = prompt("Wpisz, po spacji, ID użytkowników, których chcesz dodać", '');
    let users = `:${userid}:`+ user.split(' ').join(':') +":";

    if (groupname && groupname !== '' && users && users !== '') {
        fetch("../backend/ajax.php?action=create&id="+ userid +"&group="+ groupname +"&users="+ users);
        setTimeout(window.location.reload(true), 500);
    }
}

if (messid !== 0) {
    window.addEventListener("load", function() {
        mess.scrollTo(0, mess.scrollHeight);

        setInterval(() => {
            if (mess.scrollTop == 0 && !generatingMess) {
                generatingMess = true;
                count += 25;
                mess.scrollTo(0, 30);
                fetch("../backend/ajax.php?id="+ userid +"&mess="+ messid +"&action=generate&count="+ count)
                    .then(response => response.json())
                    .then(json => {
                        let lastDiv = document.querySelectorAll('.mess')[0];
                        let lastSender = document.querySelectorAll('.user')[0];
        
                        for(const text of json) {
                            if ((text['sender']+"").equalsIgnoreCase(lastSender.textContent)) {
                                let newDiv = document.createElement('div');
                                newDiv.classList.add(text['senderID'] == userid ? 'yours' : 'theirs');
                                newDiv.classList.add('mess');
                                newDiv.innerHTML = '<p>'+ text['content'] +'</p>';
                                mess.insertBefore(newDiv, lastDiv);
                                lastDiv = newDiv;
                            }
                        }
                        generatingMess = false;
                    });
            }

            if (lastMess) {
                fetch("../backend/ajax.php?id="+ userid +"&mess="+ messid +"&action=refresh")
                    .then(response => response.json())
                    .then(json => json[0])
                    .then(json => {
                        console.log(json['content']);
                        if (lastMess.content !== json['content']) {
                            let lastSender = document.querySelectorAll('.user')[document.querySelectorAll('.user').length-1];

                            if (!lastSender || lastSender.textContent !== json['sender']) {
                                let newSender = document.createElement('div');
                                newSender.classList.add(json['senderID'] === userid ? 'yours' : 'theirs');
                                newSender.classList.add('user');
                                newSender.innerHTML = '<p>'+ json['sender'] +'</p>';
                                mess.appendChild(newSender);
                            }

                            let newDiv = document.createElement('div');
                            newDiv.classList.add(json['senderID'] == userid ? 'yours' : 'theirs');
                            newDiv.classList.add('mess');
                            newDiv.innerHTML = '<p>'+ json['content'] +'</p>';
                            mess.appendChild(newDiv);

                            lastMess = { sender: json['sender'], content: json['content'] }
                            mess.scrollTo(0, mess.scrollHeight);
                        }
                    });
            }
        }, 500);
    });

    document.getElementById('sender').addEventListener('click', () => {
        sendMessage(text.value);
    });

    document.getElementsByTagName('form')[0].addEventListener("keypress", e => {
        if (e.key.equalsIgnoreCase("enter")) {
            e.preventDefault();
            sendMessage(text.value);
        }
    })

    function sendMessage(content) {
        if (text.value.length <= 0 || text.value.length > 255) {
            alert("Tekst musi mieć długość pomiędzy 0 a 255 znaków");
            return;
        }

        if (lastMess && text.value === lastMess.content) {
            alert("Nie możesz wysłać dwóch identycznych wiadomości");
            return;
        }

        fetch('../backend/ajax.php?action=send&id='+ userid +'&mess='+ messid +'&count='+ count +'&content='+ encodeURIComponent(content));
        text.value = '';
    }
}