var count = 25;

console.log(`id: ${userid}; mess: ${messid}; count: ${count}`);

document.addEventListener("DOMContentLoaded", () => { 
    document.querySelector('#mess').scrollTo(document.documentElement.offsetHeight - window.innerHeight); 
});

function redirect(messid) {
    window.location.replace(`./index.php?id=${userid}&mess=${messid}&count=${count}`);
    this.messid = messid;
}

function back() {
    window.location.replace(`./index.php?id=${this.userid}`);
    this.messid = 0;
    this.count = 25;
}

window.addEventListener('scroll', () => {
    console.log(window.scrollY);
    if (window.scrollY === 0) console.log("ON TOP");
});