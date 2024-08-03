const voteData = [
    {
        name: "ArenaTop100",
        interval: "every 12 hours",
        reward: "2 COINS",
        imgSrc: "path_to_image/arena.png" // replace with actual image path
    },
    {
        name: "Top100Arena",
        interval: "every 24 hours",
        reward: "4 COINS",
        imgSrc: "path_to_image/top100arena.png" // replace with actual image path
    },
    {
        name: "GTop100",
        interval: "every 24 hours",
        reward: "4 COINS",
        imgSrc: "path_to_image/gtop100.png" // replace with actual image path
    },
    {
        name: "TopG",
        interval: "every 12 hours",
        reward: "2 COINS",
        imgSrc: "path_to_image/topg.png" // replace with actual image path
    },
    {
        name: "XtremeTop100",
        interval: "every 12 hours",
        reward: "2 COINS",
        imgSrc: "path_to_image/xtreme.png" // replace with actual image path
    },
    {
        name: "EtopGames",
        interval: "every 20 hours",
        reward: "1 COINS",
        imgSrc: "path_to_image/etopgames.png" // replace with actual image path
    }
];

const voteContainer = document.getElementById('voteContainer');

voteData.forEach(vote => {
    const voteBox = document.createElement('div');
    voteBox.className = 'vote-box';
    voteBox.innerHTML = `
        <h3>${vote.name}</h3>
        <p>You can vote ${vote.interval}.</p>
        <img src="${vote.imgSrc}" alt="${vote.name}">
        <p>YOU WILL RECEIVE</p>
        <p>${vote.reward}</p>
    `;
    voteContainer.appendChild(voteBox);
});
