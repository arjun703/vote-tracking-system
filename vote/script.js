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

function handleVoteDataClick(event){
    
}

voteContainer.innerHTML = voteData.map(vote => {
    return`
        <div  class="voting-data-wrapper col-12 col-sm-6 col-md-4">
            <div onClick="handleVoteDataClick(event)" class="voting-data">
                <h3>${vote.name}</h3>
                <p>You can vote ${vote.interval}.</p>
                <img src="${vote.imgSrc}" alt="${vote.name}">
                <p>YOU WILL RECEIVE</p>
                <p>${vote.reward}</p>
            </div>
        </div>
    `
}).join('');
