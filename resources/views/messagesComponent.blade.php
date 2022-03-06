<table id="messagesTable" class="table table-sm">
    <thead>
    <tr>
        <th scope="col" width="20%"><small><b>Date and time</b></small></th>
        <th scope="col" width="30%"><small><b>Subject</b></small></th>
        <th scope="col" width="50%"><small><b>Message</b></small></th>
        <th scope="col" width="5%"><small></small></th>
    </tr>
    </thead>
    <tbody>
    @forelse($messages as $message)
        <tr>
            <td class="message-created-at" scope="col" width="20%"><small>{{$message->created_at->toString()}}<small></td>
            <td class="message-subject" scope="col" width="25%" style="word-wrap: break-word;"><small>{{$message->subject}}<small></td>
            <td class="message-message-preview" scope="col" width="50%" style="word-wrap: break-word;"><small>{!! $message->message !!}<small></td>
            <th class="message-edit-buttons" scope="col" width="5%"><a class="btn btn-sm btn-link" href="{{route('EditMessage', ['public_key' => $public_key, 'id' => $message->id, 'private_key' => $private_key])}}">Edit</a></th>
        </tr>
    @empty
        <tr>
            <td colspan=4 class="text-center pt-3">There's no messages received on this channel.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<script>
$(document).ready(function() {
    $("#messagesTable").dataTable({
        "order": []
    });
});
</script>