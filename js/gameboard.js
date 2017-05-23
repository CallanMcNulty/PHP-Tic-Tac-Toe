const search = location.search;
let paramsArray = search.substring(1,search.length).split("&");
let params = {};
for(let i=0; i<paramsArray.length; i++) {
  const keyVal = paramsArray[i].split("=");
  params[keyVal[0]] = decodeURIComponent(keyVal[1]);
}
var dontUpdate = false;
var finished = false;
params.playerId = parseInt(params.playerId);
var mainLoop = setInterval(function() {
  $.ajax({
    type: "GET",
    url: `../getGameDetails.php?player=${params.playerId}&code=${params.code}&game=${params.game}`,
    success: function(result) {
      let info = JSON.parse(result);
      if(info.success) {
        params.turn = info.turn;
        if(!dontUpdate) {
          $("#game-name").text(params.game+": Turn "+params.turn);
          let midtext = params.turn%2===params.playerId%2 ? "It's your turn." : "Waiting for your turn...";
          $("#middle-text").text(midtext);
          if(info.victor!=="#") {
            onFinish(info.victor===params.token);
          }
          drawBoard(info.state);
        }
      } else {
        onError(info.error);
      }
    }
  });
}, 5000);
let loadingCircleOrder = [0,1,2,5,8,7,6,3];
let loadingCircleIndex = 0;
let loadingInterval = setInterval(function() {
  if($("#middle-text").text()==="Loading...") {
    const allSquares = $(".square");
    allSquares.css("background-color","inherit");
    allSquares.css("box-shadow","none");
    const currentSquare = $("#"+loadingCircleOrder[loadingCircleIndex]);
    currentSquare.css("background-color","white");
    currentSquare.css("box-shadow","0 0 5px gray");
    loadingCircleIndex = loadingCircleIndex===7 ? 0 : (loadingCircleIndex + 1);
  } else {
    const allSquares = $(".square");
    allSquares.removeAttr("style");
    clearInterval(loadingInterval);
  }
}, 300);
function onFinish(youWin) {
  $("#middle-text").text(youWin ? "You win!" : "You lose.");
  $("#done").show();
  $("#new-game").show();
  finished = true;
  clearInterval(mainLoop);
}
function onError(err) {
  console.log(err);
  $("#middle-text").text('Something went wrong.');
  $("#reset").show();
  $("#done").show();
}
$(document).ready(function () {
  $('.square').click(function () {
    const s = $(this);
    if(s.attr("value")==="#" && params.turn%2===params.playerId%2 && !finished && !dontUpdate) {
      dontUpdate = true;
      drawToken(s, params.token);
      $("#middle-text").text("Calculating...");
      $.ajax({
        type: "GET",
        url: `../play.php?player=${params.playerId}&code=${params.code}&game=${params.game}&move=${s.attr("id")}`,
        success: function(result) {
          let info = JSON.parse(result);
          if(info.success) {
            drawBoard(info.board);
            dontUpdate = false;
            if(info.finished) {
              onFinish(params.turn%2===params.playerId%2);
            }
          } else {
            onError(info.error);
          }
        }
      });
    }
  });
  $(".square").mouseover(function() {
    const s = $(this);
    if(s.attr("value")==="#" && params.turn%2===params.playerId%2 && !finished && !dontUpdate)
      s.addClass("over");
  });
  $(".square").mouseleave(function() {
    $(this).removeClass("over");
  });
  $("#done").click(function() {
    window.location.href = window.location.protocol+"//"+window.location.host+`/goodbye.php?player=${params.playerId}&code=${params.code}&game=${params.game}`;
  });
  $("#reset").click(function() {
    window.location.href = window.location.href;
  });
  $("#new-game").click(function() {
    window.location.href = window.location.protocol+"//"+window.location.host;
  });
});
function drawBoard(gameState) {
  for(let i=0; i<gameState.length; i++) {
    drawToken($("#"+i), gameState[i])
  }
}
function drawToken(square, token) {
  square.empty();
  if(token==="X") {
    const ex = $(`<svg height='80px' width='80px' viewBox='0 0 100 100'>
    <polygon class='x-token' points='20,0 100,80 80,100 0,20'></polygon>
    <polygon class='x-token' points='80,0 0,80 20,100 100,20'></polygon>
    </svg>`);
    square.append(ex);
  }
  if(token==="O") {
    const ex = $(`<svg height='80px' width='80px' viewBox='0 0 100 100'>
    <circle class='o-token' cx='50' cy='50' r='50' />
    <circle cx='50' cy='50' r='25' fill='white' />
    </svg>`);
    square.append(ex);
  }
  square.attr("value", token);
}
