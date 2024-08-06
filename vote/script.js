
const voteContainer = document.getElementById('voteContainer');

function handleVoteDataClick(event){

}


// {
//     "is_logged_in": true,
//     "id": "1024",
//     "settings": {
//         "credit_system_status": "ON",
//         "credit_multiplier": {
//             "multiply_by": 2,
//             "active_on_days_of_month": [
//                 1,
//                 2,
//                 3,
//                 4,
//                 5
//             ]
//         },
//         "disclaimer_text": "Voting may take time for your credits to be recieved.",
//         "voting_websites": [
//             {
//                 "handle": "etopgames",
//                 "waiting_time_in_seconds_for_next_vote": 72000,
//                 "name": "EtopGames",
//                 "credit_count": 10,
//                 "btn_img_url": "https://www.etopgames.com/button.php?u=pwember&buttontype=static",
//                 "voting_url_without_userid": "https://www.etopgames.com/index.php?a=in&u=pwember&id=",
//                 "seconds_elapsed": 5339
//             },
//             {
//                 "handle": "arenatop100",
//                 "waiting_time_in_seconds_for_next_vote": 43200,
//                 "name": "ArenaTop100",
//                 "credit_count": 5,
//                 "btn_img_url": "https://www.arena-top100.com/images/vote/perfect-world-private-servers.png",
//                 "voting_url_without_userid": "https://www.arena-top100.com/index.php?a=in&u=pwember&id=",
//                 "seconds_elapsed": 4906
//             },
//             {
//                 "handle": "top100arena",
//                 "waiting_time_in_seconds_for_next_vote": 86400,
//                 "name": "Top100Arena",
//                 "credit_count": 10,
//                 "btn_img_url": "https://www.top100arena.com/hit/100780/big",
//                 "voting_url_without_userid": "https://www.top100arena.com/listing/100780/vote?incentive=",
//                 "seconds_elapsed": -1
//             },
//             {
//                 "handle": "gtop100",
//                 "waiting_time_in_seconds_for_next_vote": 86400,
//                 "name": "Gtop100",
//                 "credit_count": 10,
//                 "btn_img_url": "https://gtop100.com/assets/images/votebutton.jpg",
//                 "voting_url_without_userid": "https://gtop100.com/topsites/Perfect-World/sitedetails/Perfect-World-Ember-103844?vote=1&pingUsername=",
//                 "seconds_elapsed": 4165
//             },
//             {
//                 "handle": "topg",
//                 "waiting_time_in_seconds_for_next_vote": 43200,
//                 "name": "Topg",
//                 "credit_count": 10,
//                 "btn_img_url": "https://topg.org/topg2.gif",
//                 "voting_url_without_userid": "https://topg.org/perfect-world-private-servers/server-665948-",
//                 "seconds_elapsed": 3238
//             },
//             {
//                 "handle": "xtremetop100",
//                 "waiting_time_in_seconds_for_next_vote": 43200,
//                 "name": "Xtremetop100",
//                 "credit_count": 20,
//                 "btn_img_url": "https://www.xtremeTop100.com/votenew.jpg",
//                 "voting_url_without_userid": "https://www.xtremetop100.com/in.php?site=1132376111&postback=",
//                 "seconds_elapsed": 2979
//             }
//         ]
//     }
// }

async function handleVoteDataClick(event){

    const srcWebsite = event.target.getAttribute('data-src-website')

    if(srcWebsite == 'top100arena'){
        const resp =  await fetch('api/save_ip.php')
        const respJson = await resp.json()
        console.log(respJson)
    }

    const votingWebsiteDatas = window.voteData.settings.voting_websites.filter(vw => vw.handle === srcWebsite)

    if(votingWebsiteDatas.length){

        var voting_website = votingWebsiteDatas[0]

        if(voting_website.seconds_elapsed > 0 &&  (voting_website.seconds_elapsed < voting_website.waiting_time_in_seconds_for_next_vote )){
            alert('Please wait until the voting cycle resets.')
        }else{
            window.location.href  = voting_website.voting_url_without_userid + window.voteData.id
        }
    }

    // check if the user can vote

}

async function fetchDataAndRender(){

    const voteDataResponse = await fetch('api/send_voting_data.php'); 

    window.voteData = await voteDataResponse.json()

    if(voteData.is_logged_in === false){
        alert("You need to login to view this page")
        return
    }

    var today = new Date();

    // Get the day of the month (1 to 31)
    var dayOfMonth = today.getDate();

    var multiplier = 1;

    if(voteData.settings.credit_multiplier.active_on_days_of_month.includes(dayOfMonth)){
        multiplier = voteData.settings.credit_multiplier.multiply_by
    }

    voteContainer.innerHTML = window.voteData.settings.voting_websites.map(voting_website => {
        
        return`
            <div  class="voting-data-wrapper col-12 col-sm-6 col-md-4" >
                <div  data-src-website="${voting_website.handle}" onClick="handleVoteDataClick(event)" class="voting-data align-items-center d-flex justify-content-center">
                    <div>
                        <h3 class="text-yellow">${voting_website.name}</h3>
                        ${
                            voting_website.seconds_elapsed > 0 &&  (voting_website.seconds_elapsed < voting_website.waiting_time_in_seconds_for_next_vote ) 
                                ? `
                                    <p>You can vote every ${ parseInt(voting_website.waiting_time_in_seconds_for_next_vote) / 3600  } hours.</p>
                                    <div class="text-yellow">
                                        ${  Math.floor((voting_website.waiting_time_in_seconds_for_next_vote - voting_website.seconds_elapsed) / 3600)   }h remaining
                                    </div>
                                `: ''
                        }
                        <img class="pt-2" src="${voting_website.btn_img_url}" alt="${voting_website.name}" />
                        <div class="pt-2">YOU WILL RECEIVE</div>
                        <h4 class="text-yellow">${ (voting_website.credit_count) * multiplier } coins</h4>
                    </div>
                </div>
            </div>
        `
    }).join('');

    document.body.innerHTML += `
        <div class="pt-5 text-center">${window.voteData.settings.disclaimer_text}</div>
    `



}

fetchDataAndRender()