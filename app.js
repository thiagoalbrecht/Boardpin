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
	axios.post(rootURL + "update.php", 'task=' + task + '&json=' + encodeURIComponent(jsoncontent) + '&boardid=' + boardlink)
		.then(() => {
			allowUpdate = true;
		})
		.catch(err => {
			console.log(err);
		});
}

var boardpinApp = new Vue({

	el: '#app-boardpin',

	data: {
		noteItems: [],
		newNoteText: ""
	},

	methods: {

		addNote: function () {
			allowUpdate = false;
			if (this.newNoteText.trim() === "")
				return;
			let boolIsTodo = false;
			if (this.newNoteText.charAt(0) === "!") { boolIsTodo = true; this.newNoteText = this.newNoteText.substring(1) } // Remove initial ! from string for to-do items
			let newItem = { Id: GetFreeItemIndex(this.noteItems), Text: this.newNoteText, IsTodo: boolIsTodo, IsDone: false };
			let jsoncontent = JSON.stringify(newItem);
			switch (this.newNoteText) {
				case "/help":
					alert("!Something = Create a to-do item 'Something'\n\n/clear = Delete all notes from board\n\n/destroy = Delete board from existence");
					break;
				case "/clear":
					this.noteItems = [];
					postUpdate("add", jsoncontent);
					break;
				case "/destroy":
					this.noteItems = [];
					postUpdate("add", jsoncontent);
					break;
				default:
					this.noteItems.unshift(newItem);
					postUpdate("add", jsoncontent);
					break;
			}
			this.newNoteText = "";

		},

		updateNote: function (note, task) {
			let jsoncontent = JSON.stringify(this.noteItems[this.noteItems.indexOf(note)]);
			if (task === "remove") this.noteItems.splice(this.noteItems.indexOf(note), 1);
			postUpdate(task, jsoncontent);
		},

		refreshItems: function (instant) {
			let target = "json.php?";
			if (instant) target = "json.php?instant=1&"; // URL for requesting the list content without waiting for change (force update w/o long polling)
			axios.get(rootURL + target + 'boardid=' + boardlink)
				.then(response => {
					if (response.data === 404) {
						alert("This board could not be found. It might have been deleted. Please check the URL.");
					} else {
						this.noteItems = [...response.data]
						GetFreeItemIndex(this.noteItems);
						this.refreshItems();
					}
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
