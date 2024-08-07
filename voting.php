<div class="row-fluid abc">
	<div class="box span12">
		<div class="box-header well">
			<h2>Voting System</h2>
		</div>
		<div class="box-content">
			<div class="container pt-5 pb-8">
				<div class="row" id="voteContainer">

				</div>
				<!-- Dynamic content will be inserted here -->
			</div>
		</div>
	</div>
</div>




<style>
	.pb-8{
		padding-bottom:2.5rem;
	}
	.pt-5{
		padding-top: 1.5rem;
	}
.voting-data-wrapper{
    padding:8px;
    display: flex;
    align-items: stretch;
}

.voting-data{
    box-shadow: 0 0px 3px 0 black;
    border-radius: 5px;
    padding:8px;
    height: 100%;
    width: 100%;
}


.container *{
    text-align: center;
}

.voting-data:hover{
    cursor:pointer;
    box-shadow: 0 0px 8px 0 black;
}


.row {
    display: flex;
    flex-wrap: wrap;
    margin: -1rem; /* Adjust for row gutters */
}

/* Voting Data Wrapper equivalent to Bootstrap grid */
.voting-data-wrapper {
    flex: 1 1 33.33%; /* Ensure it takes up one-third of the row */
    max-width: 30%; /* Limit the maximum width */
}

/* Responsive adjustments for small and medium screens */
@media (max-width: 768px) {
    .voting-data-wrapper {
        flex: 1 1 50%; /* Two columns on small screens */
        max-width: 50%;
    }
}

@media (max-width: 576px) {
    .voting-data-wrapper {
        flex: 1 1 100%; /* One column on extra small screens */
        max-width: 100%;
    }
}

/* Align items center and flexbox utility */
.voting-data {
    display: flex;
    align-items: center;
    justify-content: center;
}


/* Padding top for images and other elements */
.pt-2 {
    padding-top: 0.5rem; /* Equivalent to pt-2 in Bootstrap */
}

/* Image styling */
.img-responsive {
    max-width: 100%; /* Ensure the image scales properly */
    height: auto; /* Maintain aspect ratio */
}

</style>



    <script>



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
    console.log("hello")
    const srcWebsite = event.target.getAttribute('data-src-website')
    if(srcWebsite == 'top100arena'){
        const resp =  await fetch('./../vote-tracking-system/vote/api/save_ip.php')
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

    const voteDataResponse = await fetch('./../vote-tracking-system/vote/api/send_voting_data.php'); 

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
            <div  class="voting-data-wrapper col-12 col-sm-6 col-md-4">
                <div   class="voting-data align-items-center d-flex justify-content-center">
                    <div>
                        <h3  class="text-yellow">${voting_website.name}</h3>
                        ${
                            voting_website.seconds_elapsed > 0 &&  (voting_website.seconds_elapsed < voting_website.waiting_time_in_seconds_for_next_vote ) 
                                ? `
                                    <p>You can vote every ${ parseInt(voting_website.waiting_time_in_seconds_for_next_vote) / 3600  } hours.</p>
                                    <div class="text-yellow">
                                        ${  Math.floor((voting_website.waiting_time_in_seconds_for_next_vote - voting_website.seconds_elapsed) / 3600)   }h remaining
                                    </div>
                                `: ` 
                                    <button data-src-website="${voting_website.handle}" style="background:black!important;;color:white;border:1px solid white; display:block; width:100%;z-index:111122;position:relative; padding: 5px 15px" onclick="handleVoteDataClick(event)">Vote</button>
                                `
                        }
                        <img  class="pt-2" src="${voting_website.btn_img_url}" alt="${voting_website.name}" />
                        <div class="pt-2">YOU WILL RECEIVE</div>
                        <h4 class="text-yellow">${ (voting_website.credit_count) * multiplier } coins</h4>
                    </div>
                </div>
            </div>
        `
    }).join('');

    document.querySelector('.container').innerHTML += `
        <div style="margin-top:4px" class="pt-5 text-center">${window.voteData.settings.disclaimer_text}</div>
    `



}

fetchDataAndRender()
    </script>
