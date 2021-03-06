window.fakeStorage = {
  _data: {},

  setItem: function (id, val) {
    return this._data[id] = String(val);
  },

  getItem: function (id) {
    return this._data.hasOwnProperty(id) ? this._data[id] : undefined;
  },

  removeItem: function (id) {
    return delete this._data[id];
  },

  clear: function () {
    return this._data = {};
  }
};

function LocalStorageManager() {
  this.bestScoreKey     = "bestScore";
  this.gameStateKey     = "gameState";

  var supported = this.localStorageSupported();
  this.storage = supported ? window.localStorage : window.fakeStorage;
}

LocalStorageManager.prototype.localStorageSupported = function () {
  var testKey = "test";

  try {
    var storage = window.localStorage;
    storage.setItem(testKey, "1");
    storage.removeItem(testKey);
    return true;
  } catch (error) {
    return false;
  }
};

// Best score getters/setters
LocalStorageManager.prototype.getBestScore = function () {
  return this.storage.getItem(this.bestScoreKey) || 0;
};

LocalStorageManager.prototype.setBestScore = function (score) {
  this.storage.setItem(this.bestScoreKey, score);
};

// Best score getters/setters
LocalStorageManager.prototype.getBestRemoteScore = function () {
  var http = new XMLHttpRequest();
  var url = 'scripts/getScore.php';
  http.open('GET', url, false);

  //Send the proper header information along with the request
  http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  http.send();

  return parseInt(http.responseText);
};

LocalStorageManager.prototype.setBestRemoteScore = function (score) {
  var http = new XMLHttpRequest();
  var url = 'scripts/setScore.php';
  var params = 'highest_score='+score
  http.open('POST', url, true);

  //Send the proper header information along with the request
  http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

  http.onreadystatechange = function() {//Call a function when the state changes.
      if(http.readyState == 4 && http.status == 200) {
          console.log("set");
      }
  }
  http.send(params);
};

// Game state getters/setters and clearing
LocalStorageManager.prototype.getGameState = function () {
  var stateJSON = this.storage.getItem(this.gameStateKey);
  return stateJSON ? JSON.parse(stateJSON) : {};
};

LocalStorageManager.prototype.getRemoteGameState = function() {
  var http = new XMLHttpRequest();
  var url = 'scripts/getGame.php';
  http.open('GET', url, false);

  //Send the proper header information along with the request
  http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  http.send();

  return JSON.parse(http.responseText);
}

LocalStorageManager.prototype.setGameState = function (gameState) {
  this.storage.setItem(this.gameStateKey, JSON.stringify(gameState));
};

LocalStorageManager.prototype.setRemoteGameState = function (gameState, async=true) {
  var http = new XMLHttpRequest();
  var url = 'scripts/setGame.php';
  var params = "gamestate="+JSON.stringify(gameState);
  
  http.open('POST', url, async);

  //Send the proper header information along with the request
  http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  if(async){
    http.onreadystatechange = function() {//Call a function when the state changes.
        if(http.readyState == 4 && http.status == 200) {
            console.log("set game");
        }
    }
  }
  http.send(params);
};


LocalStorageManager.prototype.clearGameState = function () {
  this.storage.removeItem(this.gameStateKey);
  this.setRemoteGameState({}, false);
};

LocalStorageManager.prototype.clearLocalGameState = function () {
  this.storage.removeItem(this.gameStateKey);
} 