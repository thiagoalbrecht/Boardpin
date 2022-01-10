var itemIndex = 0;
var rootURL = "https://boardpin.xyz/handler/"; // Handler URL
var allowUpdate = true;

function GetFreeItemIndex(objlist) {
	if (typeof objlist[0] !== 'undefined') itemIndex = objlist[0]['Id'];
	else itemIndex = 0;
	itemIndex++;
	return itemIndex;
}

function postUpdate(task, jsoncontent) {
	axios.post(rootURL + "update.php", 'task=' + task + '&json=' + jsoncontent + '&boardid=' + boardlink)
		.then(() => {
			allowUpdate = true;
		})
		.catch(err => {
			console.log(err);
		});
}

var todoListApp = new Vue({

	el: '#app-boardpin',

	data: {
		todoItems: [],
		newTodoText: ""
	},

	methods: {

		addTodo: function () {
			allowUpdate = false;
			if (this.newTodoText.trim() === "")
				return;

			let newItem = { Id: GetFreeItemIndex(this.todoItems), Text: this.newTodoText, IsDone: false };
			let jsoncontent = JSON.stringify(newItem);
			switch (this.newTodoText) {
				case "/help":
					alert("!Something = Create a to-do item 'Something'\n/clear = Delete all notes from board");
					break;
				case "/clear":
					this.todoItems = [];
					postUpdate("add", jsoncontent);
					break;

				default:
					this.todoItems.unshift(newItem);
					postUpdate("add", jsoncontent);
					break;
			}
			this.newTodoText = "";

		},

		removeTodo: function (todo) {
			console.log("Changed");
			let jsoncontent = JSON.stringify(this.todoItems[this.todoItems.indexOf(todo)]);
			this.todoItems.splice(this.todoItems.indexOf(todo), 1);
			postUpdate("remove", jsoncontent);
		},

		refreshItems: function (instant) {
			let target = "json.php";
			if (instant) target = "json.php?instant=1"; // URL for requesting the list content without waiting for change (force update w/o long polling)
			axios.get(rootURL + target + '&boardid=' + boardlink)
				.then(response => {
					this.todoItems = [...response.data]
					GetFreeItemIndex(this.todoItems);
					this.refreshItems();
				})
		},
		copytoClipboard: function (url) {
			navigator.clipboard.writeText(url)
				.catch(
					function () {
						alert("Couldn't copy to clipboard. Please manually select and copy the link."); // Error
					});
		}

	},

	mounted() {
		this.refreshItems(true);
	}
});
