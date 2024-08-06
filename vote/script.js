
const voteContainer = document.getElementById('voteContainer');

function handleVoteDataClick(event){

}

async function fetchDataAndRender(){

    const voteDataResponse = await fetch('api/send_voting_data.php'); 
    
    const voteData = await voteDataResponse.json()
    
    console.log(voteData);
    
    voteContainer.innerHTML = voteData.map(vote => {
        return`
            <div  class="voting-data-wrapper col-12 col-sm-6 col-md-4">
                <div onClick="handleVoteDataClick(event)" class="voting-data">
                    <h3 class="text-yellow">${vote.name}</h3>
                    <p>You can vote ${vote.interval}.</p>
                    <img src="${vote.imgSrc}" alt="${vote.name}">
                    <p>YOU WILL RECEIVE</p>
                    <p class="text-yellow">${vote.reward}</p>
                </div>
            </div>
        `
    }).join('');

}