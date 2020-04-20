function GameManager(size, InputManager, Actuator, StorageManager, username) {
  this.size           = size; // Size of the grid
  this.inputManager   = new InputManager;
  this.storageManager = new StorageManager;
  this.actuator       = new Actuator;

  this.startTiles     = 4;

  this.inputManager.on("move", this.move.bind(this));
  this.inputManager.on("restart", this.restart.bind(this));
  this.inputManager.on("keepPlaying", this.keepPlaying.bind(this));
  this.username = username;
  this.setup();
}

// Restart the game
GameManager.prototype.restart = function () {
  this.storageManager.clearGameState();
  this.actuator.continueGame(); // Clear the game won/lost message
  this.setup();
};

// Keep playing after winning (allows going over 2048)
GameManager.prototype.keepPlaying = function () {
  this.keepPlaying = true;
  this.actuator.continueGame(); // Clear the game won/lost message
};

// Return true if the game is lost, or has won and the user hasn't kept playing
GameManager.prototype.isGameTerminated = function () {
  return this.over || (this.won && !this.keepPlaying);
};

// Set up the game
GameManager.prototype.setup = function () {
  var previousState = this.storageManager.getRemoteGameState();

  // Reload the game from a previous game if present
  if (JSON.stringify(previousState)!=JSON.stringify({})) {
    this.grid        = new Grid(previousState.grid.size,
                                previousState.grid.cells); // Reload grid
    this.score       = previousState.score;
    this.over        = previousState.over;
    this.won         = previousState.won;
    this.keepPlaying = previousState.keepPlaying;
  } else {
    this.grid        = new Grid(this.size);
    this.score       = 0;
    this.over        = false;
    this.won         = false;
    this.keepPlaying = false;

    // Add the initial tiles
    this.addStartTiles();
  }

  // Update the actuator
  this.actuate();
};

// Set up the initial tiles to start the game with
GameManager.prototype.addStartTiles = function () {
  for (var i = 0; i < this.startTiles; i++) {
    this.addRandomTile();
  }
};

// Adds a tile in a random position
GameManager.prototype.addRandomTile = function () {
  if (this.grid.cellsAvailable()) {
    var value = 2;
    var prob = Math.random();
    //console.log(prob);
    if (prob < .8){
      op = '+';
      value = Math.random() < 0.9 ? 2 : 4;
    }
    else if(prob<.82){
      op='0';
      value=0;
    }
    else if(prob<.91){
      op='*';
    }
    else if(prob<1){
      op='/';
    }
    //console.log(op);
    var tile = new Tile(this.grid.randomAvailableCell(), value, op);

    this.grid.insertTile(tile);
  }
};

GameManager.prototype.refreshHighScoreList = function() {



  var http = new XMLHttpRequest();
  var url = 'scripts/getHighScoreList.php';
  http.open('GET', url, true);

  //Send the proper header information along with the request
  http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

  http.onreadystatechange = function() {//Call a function when the state changes.
      if(http.readyState == 4 && http.status == 200) {
          let data = JSON.parse(http.responseText);
          let menu = document.getElementById('highscores-list');
          while (menu.firstChild) {
              menu.removeChild(menu.firstChild);
          }
          var table = document.createElement('table');
          for (var i = 0; i < data.length; i++){
              var tr = document.createElement('tr');   

              var td1 = document.createElement('td');
              var td2 = document.createElement('td');
              var td3 = document.createElement('td');

              var text1 = document.createTextNode(i+1);
              var text2 = document.createTextNode(data[i][0]);
              var text3 = document.createTextNode(data[i][1]);

              td1.appendChild(text1);
              td2.appendChild(text2);
              td3.appendChild(text3);
              tr.appendChild(td1);
              tr.appendChild(td2);
              tr.appendChild(td3);

              table.appendChild(tr);
          }
          menu.appendChild(table);
      }
  }
  http.send();

  
}

// Sends the updated grid to the actuator
GameManager.prototype.actuate = function () {
  if (this.storageManager.getBestScore() < this.score) {
    this.storageManager.setBestScore(this.score);
    this.storageManager.setBestRemoteScore(this.score, this.username);
  }

  this.refreshHighScoreList()

  // Clear the state when the game is over (game over only, not win)
  if (this.over) {
    this.storageManager.clearGameState();
  } else {
    this.storageManager.setGameState(this.serialize());
    this.storageManager.setRemoteGameState(this.serialize());
  }

  this.actuator.actuate(this.grid, {
    score:      this.score,
    over:       this.over,
    won:        this.won,
    bestScore:  this.storageManager.getBestScore(),
    terminated: this.isGameTerminated()
  });

};

// Represent the current game as an object
GameManager.prototype.serialize = function () {
  return {
    grid:        this.grid.serialize(),
    score:       this.score,
    over:        this.over,
    won:         this.won,
    keepPlaying: this.keepPlaying
  };
};

// Save all tile positions and remove merger info
GameManager.prototype.prepareTiles = function () {
  this.grid.eachCell(function (x, y, tile) {
    if (tile) {
      tile.mergedFrom = null;
      tile.savePosition();
    }
  });
};

// Move a tile and its representation
GameManager.prototype.moveTile = function (tile, cell) {
  this.grid.cells[tile.x][tile.y] = null;
  this.grid.cells[cell.x][cell.y] = tile;
  tile.updatePosition(cell);
};

// Move tiles on the grid in the specified direction
GameManager.prototype.move = function (direction) {
  // 0: up, 1: right, 2: down, 3: left
  var self = this;
  self.score = 0;
  if (this.isGameTerminated()) return; // Don't do anything if the game's over

  var cell, tile;

  var vector     = this.getVector(direction);
  var traversals = this.buildTraversals(vector);
  var moved      = false;

  // Save the current tile positions and remove merger information
  this.prepareTiles();

  // Traverse the grid in the right direction and move tiles
  traversals.x.forEach(function (x) {
    traversals.y.forEach(function (y) {
      cell = { x: x, y: y };
      tile = self.grid.cellContent(cell);

      if (tile) {
        var positions = self.findFarthestPosition(cell, vector);
        var next      = self.grid.cellContent(positions.next);
        console.log("tile", tile);
        console.log("next", next);
        // Only one merger per row traversal?
        if (next && !next.mergedFrom) {
          // execute regular merge if values equal and both + op
          if(next.value === tile.value && next.op==tile.op && next.op=='+'){
            var merged = new Tile(positions.next, tile.value * 2, '+');
            merged.mergedFrom = [tile, next];

            self.grid.insertTile(merged);
            self.grid.removeTile(tile);

            // Converge the two tiles' positions
            tile.updatePosition(positions.next);

          }
          else if(next.op==tile.op && next.op=='+' && next.value!=tile.value){
            self.moveTile(tile, positions.farthest);
          }
          // execute special merge if one if + but not both
          else if((next.op=='+' || tile.op=='+') && !(next.op=='+' && tile.op=='+')){
            var opTile, valTile;
            var newValue = null;
            if (next.op=='+'){
               opTile = tile;
               valTile = next;
            }
            else{
              opTile = next;
              valTile = tile;
            }
            if(opTile.op == '*'){
              newValue = valTile.value * opTile.value;
            }
            else if(opTile.op == '/'){
              if(valTile.value >= 2*opTile.value){
                newValue = valTile.value / opTile.value;
              }
              // if the number is too small to divide by this op tile
              else{
                self.moveTile(tile, positions.farthest);
              }
            }
            else if(opTile.op=='0'){
              self.grid.removeTile(tile);
              self.grid.removeTile(next);
              tile.updatePosition(positions.next);
            }

            if(newValue!=null){
              var merged = new Tile(positions.next, newValue, '+');
              merged.mergedFrom = [tile, next];

              self.grid.insertTile(merged);
              self.grid.removeTile(tile);
              tile.updatePosition(positions.next);
            }
            else{
              console.log(next);
              console.log(tile);
            }
          }
          else if(next.op!='+' && tile.op!='+'){
            // both are ops
            console.log('both ops!')
            var ops = new Map();
            ops.set(next.op, next);
            ops.set(tile.op, tile);
            console.log(ops.size);
            console.log(ops);
            console.log(ops.has('*') && ops.size==1)
            if(ops.has('*') && ops.has('/')){
              console.log('ops cancel out');
              if(ops.get('*').value == ops.get('/').value){
                self.grid.removeTile(tile);
                self.grid.removeTile(next);
                tile.updatePosition(positions.next);  
              }
              else if(ops['*'].value > ops['/'].value){
                var merged = new Tile(positions.next, ops['*'].value / ops['/'].value, '*');
                merged.mergedFrom = [tile, next];

                self.grid.insertTile(merged);
                self.grid.removeTile(tile);

                // Converge the two tiles' positions
                tile.updatePosition(positions.next); 
              }

            }
            else if((ops.has('/') || ops.has('*')) && ops.size==1){
              console.log('** merged');
              var merged = new Tile(positions.next, next.value * tile.value, tile.op);
              merged.mergedFrom = [tile, next];

              self.grid.insertTile(merged);
              self.grid.removeTile(tile);

              // Converge the two tiles' positions
              tile.updatePosition(positions.next); 
            }
            else if(ops.has('0')){
              self.grid.removeTile(tile);
              self.grid.removeTile(next);
              tile.updatePosition(positions.next);
            }
          }
        } else {
          self.moveTile(tile, positions.farthest);
        }

        if (!self.positionsEqual(cell, tile)) {
          moved = true; // The tile moved from its original cell!
        }
      }
    });
  });

  if (moved) {
    for (var x = 0; x < this.size; x++) {
      for (var y = 0; y < this.size; y++) {
        tile = this.grid.cellContent({ x: x, y: y });
        console.log(tile);
        if(tile){
          if(tile.op=='+'){
            self.score += tile.value;
          } 
        }
      }
    }
    this.addRandomTile();

    if (!this.movesAvailable()) {
      this.over = true; // Game over!
    }


    this.actuate();
  }
};

// Get the vector representing the chosen direction
GameManager.prototype.getVector = function (direction) {
  // Vectors representing tile movement
  var map = {
    0: { x: 0,  y: -1 }, // Up
    1: { x: 1,  y: 0 },  // Right
    2: { x: 0,  y: 1 },  // Down
    3: { x: -1, y: 0 }   // Left
  };

  return map[direction];
};

// Build a list of positions to traverse in the right order
GameManager.prototype.buildTraversals = function (vector) {
  var traversals = { x: [], y: [] };

  for (var pos = 0; pos < this.size; pos++) {
    traversals.x.push(pos);
    traversals.y.push(pos);
  }

  // Always traverse from the farthest cell in the chosen direction
  if (vector.x === 1) traversals.x = traversals.x.reverse();
  if (vector.y === 1) traversals.y = traversals.y.reverse();

  return traversals;
};

GameManager.prototype.findFarthestPosition = function (cell, vector) {
  var previous;

  // Progress towards the vector direction until an obstacle is found
  do {
    previous = cell;
    cell     = { x: previous.x + vector.x, y: previous.y + vector.y };
  } while (this.grid.withinBounds(cell) &&
           this.grid.cellAvailable(cell));

  return {
    farthest: previous,
    next: cell // Used to check if a merge is required
  };
};

GameManager.prototype.movesAvailable = function () {
  return this.grid.cellsAvailable() || this.tileMatchesAvailable();
};

// Check for available matches between tiles (more expensive check)
GameManager.prototype.tileMatchesAvailable = function () {
  var self = this;

  var tile;

  for (var x = 0; x < this.size; x++) {
    for (var y = 0; y < this.size; y++) {
      tile = this.grid.cellContent({ x: x, y: y });

      if (tile) {
        for (var direction = 0; direction < 4; direction++) {
          var vector = self.getVector(direction);
          var cell   = { x: x + vector.x, y: y + vector.y };

          var other  = self.grid.cellContent(cell);
          // NOTE THIS NEEDS TO BE UPDATED FOR THE OPS
          if (other && other.value === tile.value) {
            return true; // These two tiles can be merged
          }
        }
      }
    }
  }

  return false;
};

GameManager.prototype.positionsEqual = function (first, second) {
  return first.x === second.x && first.y === second.y;
};
