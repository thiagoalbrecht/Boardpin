<?php
preg_match('/([^\/]+)\/?/', $_GET['id'], $output_array); // Find correct 'id' even if query contains slash(es)
$boardlink = "boardpin.xyz/b/" . $output_array[1];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Boardpin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.0.1/dist/darkly/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,400;0,700;1,400&amp;display=swap">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.0/css/all.css">
    <link rel="stylesheet" href="/assets/css/styles.min.css">
    <link href="/style.css" rel="stylesheet" />
    <script src="https://unpkg.com/vue@2.6.14/dist/vue.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>

<body>
    <div id="app-boardpin">
        <nav class="navbar navbar-dark navbar-expand-md bg-dark navigation-clean-button">
            <div class="container"><a class="navbar-brand" href="/" style="color: rgb(255,255,255);"><i class="fas fa-map-pin me-2"></i>Boardpin</a><button data-bs-toggle="collapse" class="navbar-toggler" data-bs-target="#navcol-1"><span class="visually-hidden">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navcol-1">
                    <ul class="navbar-nav me-auto"></ul>
                    <div class="input-group" style="max-width: 390px;"><span class="input-group-text">Board link</span><input class="form-control" type="text" value="<?php echo $boardlink; ?>" disabled><button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#qrcode"><i class="fas fa-qrcode"></i></button><button class="btn btn-primary" type="button" v-on:click="copytoClipboard('<?php echo $boardlink; ?>')"><i class="far fa-copy"></i></button></div>
                </div>
            </div>
        </nav>
        <div class="container mt-5" style="max-width: 600px;">
            <div class="input-group input-group-lg" id="commands">
                <input class="form-control" type="text" placeholder="Type something (or /help)" style="background: var(--bs-dark);color: var(--bs-white);" v-model="newNoteText" v-on:keyup.enter="addNote()"><button class="btn btn-primary" type="button" v-on:click="addNote()" onclick="return false;"><i class="fas fa-plus"></i></button>
            </div>
            <div id="main-content" class="mt-4">
                <transition-group tag="ul" name="list-animation" class="list-group">
                    <li v-for="note in noteItems" v-bind:key="note.Id" class="list-group-item d-flex justify-content-between align-items-center list-item-animation">
                        <div><input class="form-check-input me-2" type="checkbox" v-model="note.IsDone" v-on:change="logCheckbox(note)"><label v-bind:class="{ completed: note.IsDone }" class="form-check-label">{{ note.Text }}</label></div><button class="btn btn-link btn-sm" v-on:click="removeNote(note)" type="button" style="color: var(--bs-gray);"><i class="fas fa-times"></i></button>
                    </li>
                </transition-group>
            </div>
        </div>
        <div role="dialog" tabindex="-1" class="modal fade" id="qrcode">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Board link</h4><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center"><img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&amp;data=https://<?php echo $boardlink; ?>" style="width: 100%;max-width: 250px;" />
                        <h4 class="mt-2"><?php echo $boardlink; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
<script>
    let boardlink = "<?php echo $output_array[1]; ?>";
</script>
<script src="/app.js"></script>

</html>