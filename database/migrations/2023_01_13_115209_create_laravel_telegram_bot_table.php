<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_callback_query', function (Blueprint $table) {
            $table->comment('');
            $table->unsignedBigInteger('id')->primary()->comment('Unique identifier for this query');
            $table->bigInteger('user_id')->nullable()->index('user_id')->comment('Unique user identifier');
            $table->bigInteger('chat_id')->nullable()->index('chat_id')->comment('Unique chat identifier');
            $table->unsignedBigInteger('message_id')->nullable()->index('message_id')->comment('Unique message identifier');
            $table->char('inline_message_id')->nullable()->comment('Identifier of the message sent via the bot in inline mode, that originated the query');
            $table->char('chat_instance')->default('')->comment('Global identifier, uniquely corresponding to the chat to which the message with the callback button was sent');
            $table->char('data')->default('')->comment('Data associated with the callback button');
            $table->char('game_short_name')->default('')->comment('Short name of a Game to be returned, serves as the unique identifier for the game');
            $table->timestamp('created_at')->nullable()->comment('Entry date creation');

            $table->index(['chat_id', 'message_id'], 'chat_id_2');
        });

        Schema::create('bot_chat', function (Blueprint $table) {
            $table->comment('');
            $table->bigInteger('id')->primary()->comment('Unique identifier for this chat');
            $table->enum('type', ['private', 'group', 'supergroup', 'channel'])->comment('Type of chat, can be either private, group, supergroup or channel');
            $table->char('title')->nullable()->default('')->comment('Title, for supergroups, channels and group chats');
            $table->char('username')->nullable()->comment('Username, for private chats, supergroups and channels if available');
            $table->char('first_name')->nullable()->comment('First name of the other party in a private chat');
            $table->char('last_name')->nullable()->comment('Last name of the other party in a private chat');
            $table->boolean('is_forum')->nullable()->default(false)->comment('True, if the supergroup chat is a forum (has topics enabled)');
            $table->boolean('all_members_are_administrators')->nullable()->default(false)->comment('True if a all members of this group are admins');
            $table->timestamp('created_at')->nullable()->comment('Entry date creation');
            $table->timestamp('updated_at')->nullable()->comment('Entry date update');
            $table->bigInteger('old_id')->nullable()->index('old_id')->comment('Unique chat identifier, this is filled when a group is converted to a supergroup');
        });

        Schema::create('bot_chat_join_request', function (Blueprint $table) {
            $table->comment('');
            $table->bigIncrements('id')->comment('Unique identifier for this entry');
            $table->bigInteger('chat_id')->index('chat_id')->comment('Chat to which the request was sent');
            $table->bigInteger('user_id')->index('user_id')->comment('User that sent the join request');
            $table->timestamp('date')->useCurrentOnUpdate()->useCurrent()->comment('Date the request was sent in Unix time');
            $table->text('bio')->nullable()->comment('Optional. Bio of the user');
            $table->text('invite_link')->nullable()->comment('Optional. Chat invite link that was used by the user to send the join request');
            $table->timestamp('created_at')->nullable()->comment('Entry date creation');
        });

        Schema::create('bot_chat_member_updated', function (Blueprint $table) {
            $table->comment('');
            $table->bigIncrements('id')->comment('Unique identifier for this entry');
            $table->bigInteger('chat_id')->index('chat_id')->comment('Chat the user belongs to');
            $table->bigInteger('user_id')->index('user_id')->comment('Performer of the action, which resulted in the change');
            $table->timestamp('date')->useCurrentOnUpdate()->useCurrent()->comment('Date the change was done in Unix time');
            $table->text('old_chat_member')->comment('Previous information about the chat member');
            $table->text('new_chat_member')->comment('New information about the chat member');
            $table->text('invite_link')->nullable()->comment('Chat invite link, which was used by the user to join the chat; for joining by invite link events only');
            $table->timestamp('created_at')->nullable()->comment('Entry date creation');
        });

        Schema::create('bot_chosen_inline_result', function (Blueprint $table) {
            $table->comment('');
            $table->bigIncrements('id')->comment('Unique identifier for this entry');
            $table->char('result_id')->default('')->comment('The unique identifier for the result that was chosen');
            $table->bigInteger('user_id')->nullable()->index('user_id')->comment('The user that chose the result');
            $table->char('location')->nullable()->comment('Sender location, only for bots that require user location');
            $table->char('inline_message_id')->nullable()->comment('Identifier of the sent inline message');
            $table->text('query')->comment('The query that was used to obtain the result');
            $table->timestamp('created_at')->nullable()->comment('Entry date creation');
        });

        Schema::create('bot_conversation', function (Blueprint $table) {
            $table->comment('');
            $table->bigIncrements('id')->comment('Unique identifier for this entry');
            $table->bigInteger('user_id')->nullable()->index('user_id')->comment('Unique user identifier');
            $table->bigInteger('chat_id')->nullable()->index('chat_id')->comment('Unique user or chat identifier');
            $table->enum('status', ['active', 'cancelled', 'stopped'])->default('active')->index('status')->comment('Conversation state');
            $table->string('command', 160)->nullable()->default('')->comment('Default command to execute');
            $table->text('notes')->nullable()->comment('Data stored from command');
            $table->timestamp('created_at')->nullable()->comment('Entry date creation');
            $table->timestamp('updated_at')->nullable()->comment('Entry date update');
        });

        Schema::create('bot_edited_message', function (Blueprint $table) {
            $table->comment('');
            $table->bigIncrements('id')->comment('Unique identifier for this entry');
            $table->bigInteger('chat_id')->nullable()->index('chat_id')->comment('Unique chat identifier');
            $table->unsignedBigInteger('message_id')->nullable()->index('message_id')->comment('Unique message identifier');
            $table->bigInteger('user_id')->nullable()->index('user_id')->comment('Unique user identifier');
            $table->timestamp('edit_date')->nullable()->comment('Date the message was edited in timestamp format');
            $table->text('text')->nullable()->comment('For text messages, the actual UTF-8 text of the message max message length 4096 char utf8');
            $table->text('entities')->nullable()->comment('For text messages, special entities like usernames, URLs, bot commands, etc. that appear in the text');
            $table->text('caption')->nullable()->comment('For message with caption, the actual UTF-8 text of the caption');

            $table->index(['chat_id', 'message_id'], 'chat_id_2');
        });

        Schema::create('bot_inline_query', function (Blueprint $table) {
            $table->comment('');
            $table->unsignedBigInteger('id')->primary()->comment('Unique identifier for this query');
            $table->bigInteger('user_id')->nullable()->index('user_id')->comment('Unique user identifier');
            $table->char('location')->nullable()->comment('Location of the user');
            $table->text('query')->comment('Text of the query');
            $table->char('offset')->nullable()->comment('Offset of the result');
            $table->char('chat_type')->nullable()->comment('Optional. Type of the chat, from which the inline query was sent.');
            $table->timestamp('created_at')->nullable()->comment('Entry date creation');
        });

        Schema::create('bot_message', function (Blueprint $table) {
            $table->comment('');
            $table->bigInteger('chat_id')->comment('Unique chat identifier');
            $table->bigInteger('sender_chat_id')->nullable()->comment('Sender of the message, sent on behalf of a chat');
            $table->unsignedBigInteger('id')->comment('Unique message identifier');
            $table->bigInteger('message_thread_id')->nullable()->comment('Unique identifier of a message thread to which the message belongs; for supergroups only');
            $table->bigInteger('user_id')->nullable()->index('user_id')->comment('Unique user identifier');
            $table->timestamp('date')->nullable()->comment('Date the message was sent in timestamp format');
            $table->bigInteger('forward_from')->nullable()->index('forward_from')->comment('Unique user identifier, sender of the original message');
            $table->bigInteger('forward_from_chat')->nullable()->index('forward_from_chat')->comment('Unique chat identifier, chat the original message belongs to');
            $table->bigInteger('forward_from_message_id')->nullable()->comment('Unique chat identifier of the original message in the channel');
            $table->text('forward_signature')->nullable()->comment('For messages forwarded from channels, signature of the post author if present');
            $table->text('forward_sender_name')->nullable()->comment('Sender\'s name for messages forwarded from users who disallow adding a link to their account in forwarded messages');
            $table->timestamp('forward_date')->nullable()->comment('date the original message was sent in timestamp format');
            $table->boolean('is_topic_message')->nullable()->default(false)->comment('True, if the message is sent to a forum topic');
            $table->boolean('is_automatic_forward')->nullable()->default(false)->comment('True, if the message is a channel post that was automatically forwarded to the connected discussion group');
            $table->bigInteger('reply_to_chat')->nullable()->index('reply_to_chat')->comment('Unique chat identifier');
            $table->unsignedBigInteger('reply_to_message')->nullable()->index('reply_to_message')->comment('Message that this message is reply to');
            $table->bigInteger('via_bot')->nullable()->index('via_bot')->comment('Optional. Bot through which the message was sent');
            $table->timestamp('edit_date')->nullable()->comment('Date the message was last edited in Unix time');
            $table->boolean('has_protected_content')->nullable()->default(false)->comment('True, if the message can\'t be forwarded');
            $table->text('media_group_id')->nullable()->comment('The unique identifier of a media message group this message belongs to');
            $table->text('author_signature')->nullable()->comment('Signature of the post author for messages in channels');
            $table->text('text')->nullable()->comment('For text messages, the actual UTF-8 text of the message max message length 4096 char utf8mb4');
            $table->text('entities')->nullable()->comment('For text messages, special entities like usernames, URLs, bot commands, etc. that appear in the text');
            $table->text('caption_entities')->nullable()->comment('For messages with a caption, special entities like usernames, URLs, bot commands, etc. that appear in the caption');
            $table->text('audio')->nullable()->comment('Audio object. Message is an audio file, information about the file');
            $table->text('document')->nullable()->comment('Document object. Message is a general file, information about the file');
            $table->text('animation')->nullable()->comment('Message is an animation, information about the animation');
            $table->text('game')->nullable()->comment('Game object. Message is a game, information about the game');
            $table->text('photo')->nullable()->comment('Array of PhotoSize objects. Message is a photo, available sizes of the photo');
            $table->text('sticker')->nullable()->comment('Sticker object. Message is a sticker, information about the sticker');
            $table->text('video')->nullable()->comment('Video object. Message is a video, information about the video');
            $table->text('voice')->nullable()->comment('Voice Object. Message is a Voice, information about the Voice');
            $table->text('video_note')->nullable()->comment('VoiceNote Object. Message is a Video Note, information about the Video Note');
            $table->text('caption')->nullable()->comment('For message with caption, the actual UTF-8 text of the caption');
            $table->text('contact')->nullable()->comment('Contact object. Message is a shared contact, information about the contact');
            $table->text('location')->nullable()->comment('Location object. Message is a shared location, information about the location');
            $table->text('venue')->nullable()->comment('Venue object. Message is a Venue, information about the Venue');
            $table->text('poll')->nullable()->comment('Poll object. Message is a native poll, information about the poll');
            $table->text('dice')->nullable()->comment('Message is a dice with random value from 1 to 6');
            $table->text('new_chat_members')->nullable()->comment('List of unique user identifiers, new member(s) were added to the group, information about them (one of these members may be the bot itself)');
            $table->bigInteger('left_chat_member')->nullable()->index('left_chat_member')->comment('Unique user identifier, a member was removed from the group, information about them (this member may be the bot itself)');
            $table->char('new_chat_title')->nullable()->comment('A chat title was changed to this value');
            $table->text('new_chat_photo')->nullable()->comment('Array of PhotoSize objects. A chat photo was change to this value');
            $table->boolean('delete_chat_photo')->nullable()->default(false)->comment('Informs that the chat photo was deleted');
            $table->boolean('group_chat_created')->nullable()->default(false)->comment('Informs that the group has been created');
            $table->boolean('supergroup_chat_created')->nullable()->default(false)->comment('Informs that the supergroup has been created');
            $table->boolean('channel_chat_created')->nullable()->default(false)->comment('Informs that the channel chat has been created');
            $table->text('message_auto_delete_timer_changed')->nullable()->comment('MessageAutoDeleteTimerChanged object. Message is a service message: auto-delete timer settings changed in the chat');
            $table->bigInteger('migrate_to_chat_id')->nullable()->index('migrate_to_chat_id')->comment('Migrate to chat identifier. The group has been migrated to a supergroup with the specified identifier');
            $table->bigInteger('migrate_from_chat_id')->nullable()->index('migrate_from_chat_id')->comment('Migrate from chat identifier. The supergroup has been migrated from a group with the specified identifier');
            $table->text('pinned_message')->nullable()->comment('Message object. Specified message was pinned');
            $table->text('invoice')->nullable()->comment('Message is an invoice for a payment, information about the invoice');
            $table->text('successful_payment')->nullable()->comment('Message is a service message about a successful payment, information about the payment');
            $table->text('connected_website')->nullable()->comment('The domain name of the website on which the user has logged in.');
            $table->text('passport_data')->nullable()->comment('Telegram Passport data');
            $table->text('proximity_alert_triggered')->nullable()->comment('Service message. A user in the chat triggered another user\'s proximity alert while sharing Live Location.');
            $table->text('forum_topic_created')->nullable()->comment('Service message: forum topic created');
            $table->text('forum_topic_closed')->nullable()->comment('Service message: forum topic closed');
            $table->text('forum_topic_reopened')->nullable()->comment('Service message: forum topic reopened');
            $table->text('video_chat_scheduled')->nullable()->comment('Service message: video chat scheduled');
            $table->text('video_chat_started')->nullable()->comment('Service message: video chat started');
            $table->text('video_chat_ended')->nullable()->comment('Service message: video chat ended');
            $table->text('video_chat_participants_invited')->nullable()->comment('Service message: new participants invited to a video chat');
            $table->text('web_app_data')->nullable()->comment('Service message: data sent by a Web App');
            $table->text('reply_markup')->nullable()->comment('Inline keyboard attached to the message');

            $table->primary(['chat_id', 'id']);
            $table->index(['reply_to_chat', 'reply_to_message'], 'reply_to_chat_2');
        });

        Schema::create('bot_poll', function (Blueprint $table) {
            $table->comment('');
            $table->unsignedBigInteger('id')->primary()->comment('Unique poll identifier');
            $table->text('question')->comment('Poll question');
            $table->text('options')->comment('List of poll options');
            $table->unsignedInteger('total_voter_count')->nullable()->comment('Total number of users that voted in the poll');
            $table->boolean('is_closed')->nullable()->default(false)->comment('True, if the poll is closed');
            $table->boolean('is_anonymous')->nullable()->default(true)->comment('True, if the poll is anonymous');
            $table->char('type')->nullable()->comment('Poll type, currently can be “regular” or “quiz”');
            $table->boolean('allows_multiple_answers')->nullable()->default(false)->comment('True, if the poll allows multiple answers');
            $table->unsignedInteger('correct_option_id')->nullable()->comment('0-based identifier of the correct answer option. Available only for polls in the quiz mode, which are closed, or was sent (not forwarded) by the bot or to the private chat with the bot.');
            $table->string('explanation')->nullable()->comment('Text that is shown when a user chooses an incorrect answer or taps on the lamp icon in a quiz-style poll, 0-200 characters');
            $table->text('explanation_entities')->nullable()->comment('Special entities like usernames, URLs, bot commands, etc. that appear in the explanation');
            $table->unsignedInteger('open_period')->nullable()->comment('Amount of time in seconds the poll will be active after creation');
            $table->timestamp('close_date')->nullable()->comment('Point in time (Unix timestamp) when the poll will be automatically closed');
            $table->timestamp('created_at')->nullable()->comment('Entry date creation');
        });

        Schema::create('bot_poll_answer', function (Blueprint $table) {
            $table->comment('');
            $table->unsignedBigInteger('poll_id')->comment('Unique poll identifier');
            $table->bigInteger('user_id')->comment('The user, who changed the answer to the poll');
            $table->text('option_ids')->comment('0-based identifiers of answer options, chosen by the user. May be empty if the user retracted their vote.');
            $table->timestamp('created_at')->nullable()->comment('Entry date creation');

            $table->primary(['poll_id', 'user_id']);
        });

        Schema::create('bot_pre_checkout_query', function (Blueprint $table) {
            $table->comment('');
            $table->unsignedBigInteger('id')->primary()->comment('Unique query identifier');
            $table->bigInteger('user_id')->nullable()->index('user_id')->comment('User who sent the query');
            $table->char('currency', 3)->nullable()->comment('Three-letter ISO 4217 currency code');
            $table->bigInteger('total_amount')->nullable()->comment('Total price in the smallest units of the currency');
            $table->char('invoice_payload')->default('')->comment('Bot specified invoice payload');
            $table->char('shipping_option_id')->nullable()->comment('Identifier of the shipping option chosen by the user');
            $table->text('order_info')->nullable()->comment('Order info provided by the user');
            $table->timestamp('created_at')->nullable()->comment('Entry date creation');
        });

        Schema::create('bot_request_limiter', function (Blueprint $table) {
            $table->comment('');
            $table->bigIncrements('id')->comment('Unique identifier for this entry');
            $table->char('chat_id')->nullable()->comment('Unique chat identifier');
            $table->char('inline_message_id')->nullable()->comment('Identifier of the sent inline message');
            $table->char('method')->nullable()->comment('Request method');
            $table->timestamp('created_at')->nullable()->comment('Entry date creation');
        });

        Schema::create('bot_shipping_query', function (Blueprint $table) {
            $table->comment('');
            $table->unsignedBigInteger('id')->primary()->comment('Unique query identifier');
            $table->bigInteger('user_id')->nullable()->index('user_id')->comment('User who sent the query');
            $table->char('invoice_payload')->default('')->comment('Bot specified invoice payload');
            $table->char('shipping_address')->default('')->comment('User specified shipping address');
            $table->timestamp('created_at')->nullable()->comment('Entry date creation');
        });

        Schema::create('bot_telegram_update', function (Blueprint $table) {
            $table->comment('');
            $table->unsignedBigInteger('id')->primary()->comment('Update\'s unique identifier');
            $table->bigInteger('chat_id')->nullable()->comment('Unique chat identifier');
            $table->unsignedBigInteger('message_id')->nullable()->index('message_id')->comment('New incoming message of any kind - text, photo, sticker, etc.');
            $table->unsignedBigInteger('edited_message_id')->nullable()->index('edited_message_id')->comment('New version of a message that is known to the bot and was edited');
            $table->unsignedBigInteger('channel_post_id')->nullable()->index('channel_post_id')->comment('New incoming channel post of any kind - text, photo, sticker, etc.');
            $table->unsignedBigInteger('edited_channel_post_id')->nullable()->index('edited_channel_post_id')->comment('New version of a channel post that is known to the bot and was edited');
            $table->unsignedBigInteger('inline_query_id')->nullable()->index('inline_query_id')->comment('New incoming inline query');
            $table->unsignedBigInteger('chosen_inline_result_id')->nullable()->index('chosen_inline_result_id')->comment('The result of an inline query that was chosen by a user and sent to their chat partner');
            $table->unsignedBigInteger('callback_query_id')->nullable()->index('callback_query_id')->comment('New incoming callback query');
            $table->unsignedBigInteger('shipping_query_id')->nullable()->index('shipping_query_id')->comment('New incoming shipping query. Only for invoices with flexible price');
            $table->unsignedBigInteger('pre_checkout_query_id')->nullable()->index('pre_checkout_query_id')->comment('New incoming pre-checkout query. Contains full information about checkout');
            $table->unsignedBigInteger('poll_id')->nullable()->index('poll_id')->comment('New poll state. Bots receive only updates about polls, which are sent or stopped by the bot');
            $table->unsignedBigInteger('poll_answer_poll_id')->nullable()->index('poll_answer_poll_id')->comment('A user changed their answer in a non-anonymous poll. Bots receive new votes only in polls that were sent by the bot itself.');
            $table->unsignedBigInteger('my_chat_member_updated_id')->nullable()->index('my_chat_member_updated_id')->comment('The bot\'s chat member status was updated in a chat. For private chats, this update is received only when the bot is blocked or unblocked by the user.');
            $table->unsignedBigInteger('chat_member_updated_id')->nullable()->index('chat_member_updated_id')->comment('A chat member\'s status was updated in a chat. The bot must be an administrator in the chat and must explicitly specify “chat_member” in the list of allowed_updates to receive these updates.');
            $table->unsignedBigInteger('chat_join_request_id')->nullable()->index('chat_join_request_id')->comment('A request to join the chat has been sent');

            $table->index(['chat_id', 'channel_post_id'], 'chat_id');
            $table->index(['chat_id', 'message_id'], 'chat_message_id');
        });

        Schema::create('bot_user', function (Blueprint $table) {
            $table->comment('');
            $table->bigInteger('id')->primary()->comment('Unique identifier for this user or bot');
            $table->boolean('is_bot')->nullable()->default(false)->comment('True, if this user is a bot');
            $table->char('first_name')->default('')->comment('User\'s or bot\'s first name');
            $table->char('last_name')->nullable()->comment('User\'s or bot\'s last name');
            $table->char('username', 191)->nullable()->index('username')->comment('User\'s or bot\'s username');
            $table->char('language_code', 10)->nullable()->comment('IETF language tag of the user\'s language');
            $table->boolean('is_premium')->nullable()->default(false)->comment('True, if this user is a Telegram Premium user');
            $table->boolean('added_to_attachment_menu')->nullable()->default(false)->comment('True, if this user added the bot to the attachment menu');
            $table->timestamp('created_at')->nullable()->comment('Entry date creation');
            $table->timestamp('updated_at')->nullable()->comment('Entry date update');
        });

        Schema::create('bot_user_chat', function (Blueprint $table) {
            $table->comment('');
            $table->bigInteger('user_id')->comment('Unique user identifier');
            $table->bigInteger('chat_id')->index('chat_id')->comment('Unique user or chat identifier');

            $table->primary(['user_id', 'chat_id']);
        });

        Schema::table('bot_callback_query', function (Blueprint $table) {
            $table->foreign(['chat_id', 'message_id'], 'bot_callback_query_ibfk_2')->references(['chat_id', 'id'])->on('bot_message');
            $table->foreign(['user_id'], 'bot_callback_query_ibfk_1')->references(['id'])->on('bot_user');
        });

        Schema::table('bot_chat_join_request', function (Blueprint $table) {
            $table->foreign(['user_id'], 'bot_chat_join_request_ibfk_2')->references(['id'])->on('bot_user');
            $table->foreign(['chat_id'], 'bot_chat_join_request_ibfk_1')->references(['id'])->on('bot_chat');
        });

        Schema::table('bot_chat_member_updated', function (Blueprint $table) {
            $table->foreign(['user_id'], 'bot_chat_member_updated_ibfk_2')->references(['id'])->on('bot_user');
            $table->foreign(['chat_id'], 'bot_chat_member_updated_ibfk_1')->references(['id'])->on('bot_chat');
        });

        Schema::table('bot_chosen_inline_result', function (Blueprint $table) {
            $table->foreign(['user_id'], 'bot_chosen_inline_result_ibfk_1')->references(['id'])->on('bot_user');
        });

        Schema::table('bot_conversation', function (Blueprint $table) {
            $table->foreign(['chat_id'], 'bot_conversation_ibfk_2')->references(['id'])->on('bot_chat');
            $table->foreign(['user_id'], 'bot_conversation_ibfk_1')->references(['id'])->on('bot_user');
        });

        Schema::table('bot_edited_message', function (Blueprint $table) {
            $table->foreign(['chat_id', 'message_id'], 'bot_edited_message_ibfk_2')->references(['chat_id', 'id'])->on('bot_message');
            $table->foreign(['user_id'], 'bot_edited_message_ibfk_3')->references(['id'])->on('bot_user');
            $table->foreign(['chat_id'], 'bot_edited_message_ibfk_1')->references(['id'])->on('bot_chat');
        });

        Schema::table('bot_inline_query', function (Blueprint $table) {
            $table->foreign(['user_id'], 'bot_inline_query_ibfk_1')->references(['id'])->on('bot_user');
        });

        Schema::table('bot_message', function (Blueprint $table) {
            $table->foreign(['reply_to_chat', 'reply_to_message'], 'bot_message_ibfk_5')->references(['chat_id', 'id'])->on('bot_message');
            $table->foreign(['via_bot'], 'bot_message_ibfk_6')->references(['id'])->on('bot_user');
            $table->foreign(['chat_id'], 'bot_message_ibfk_2')->references(['id'])->on('bot_chat');
            $table->foreign(['forward_from_chat'], 'bot_message_ibfk_4')->references(['id'])->on('bot_chat');
            $table->foreign(['left_chat_member'], 'bot_message_ibfk_7')->references(['id'])->on('bot_user');
            $table->foreign(['user_id'], 'bot_message_ibfk_1')->references(['id'])->on('bot_user');
            $table->foreign(['forward_from'], 'bot_message_ibfk_3')->references(['id'])->on('bot_user');
        });

        Schema::table('bot_poll_answer', function (Blueprint $table) {
            $table->foreign(['poll_id'], 'bot_poll_answer_ibfk_1')->references(['id'])->on('bot_poll');
        });

        Schema::table('bot_pre_checkout_query', function (Blueprint $table) {
            $table->foreign(['user_id'], 'bot_pre_checkout_query_ibfk_1')->references(['id'])->on('bot_user');
        });

        Schema::table('bot_shipping_query', function (Blueprint $table) {
            $table->foreign(['user_id'], 'bot_shipping_query_ibfk_1')->references(['id'])->on('bot_user');
        });

        Schema::table('bot_telegram_update', function (Blueprint $table) {
            $table->foreign(['inline_query_id'], 'bot_telegram_update_ibfk_5')->references(['id'])->on('bot_inline_query');
            $table->foreign(['my_chat_member_updated_id'], 'bot_telegram_update_ibfk_12')->references(['id'])->on('bot_chat_member_updated');
            $table->foreign(['callback_query_id'], 'bot_telegram_update_ibfk_7')->references(['id'])->on('bot_callback_query');
            $table->foreign(['chat_join_request_id'], 'bot_telegram_update_ibfk_14')->references(['id'])->on('bot_chat_join_request');
            $table->foreign(['pre_checkout_query_id'], 'bot_telegram_update_ibfk_9')->references(['id'])->on('bot_pre_checkout_query');
            $table->foreign(['chat_id', 'channel_post_id'], 'bot_telegram_update_ibfk_3')->references(['chat_id', 'id'])->on('bot_message');
            $table->foreign(['edited_channel_post_id'], 'bot_telegram_update_ibfk_4')->references(['id'])->on('bot_edited_message');
            $table->foreign(['poll_answer_poll_id'], 'bot_telegram_update_ibfk_11')->references(['poll_id'])->on('bot_poll_answer');
            $table->foreign(['chosen_inline_result_id'], 'bot_telegram_update_ibfk_6')->references(['id'])->on('bot_chosen_inline_result');
            $table->foreign(['chat_member_updated_id'], 'bot_telegram_update_ibfk_13')->references(['id'])->on('bot_chat_member_updated');
            $table->foreign(['shipping_query_id'], 'bot_telegram_update_ibfk_8')->references(['id'])->on('bot_shipping_query');
            $table->foreign(['edited_message_id'], 'bot_telegram_update_ibfk_2')->references(['id'])->on('bot_edited_message');
            $table->foreign(['chat_id', 'message_id'], 'bot_telegram_update_ibfk_1')->references(['chat_id', 'id'])->on('bot_message');
            $table->foreign(['poll_id'], 'bot_telegram_update_ibfk_10')->references(['id'])->on('bot_poll');
        });

        Schema::table('bot_user_chat', function (Blueprint $table) {
            $table->foreign(['chat_id'], 'bot_user_chat_ibfk_2')->references(['id'])->on('bot_chat')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['user_id'], 'bot_user_chat_ibfk_1')->references(['id'])->on('bot_user')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bot_user_chat', function (Blueprint $table) {
            $table->dropForeign('bot_user_chat_ibfk_2');
            $table->dropForeign('bot_user_chat_ibfk_1');
        });

        Schema::table('bot_telegram_update', function (Blueprint $table) {
            $table->dropForeign('bot_telegram_update_ibfk_5');
            $table->dropForeign('bot_telegram_update_ibfk_12');
            $table->dropForeign('bot_telegram_update_ibfk_7');
            $table->dropForeign('bot_telegram_update_ibfk_14');
            $table->dropForeign('bot_telegram_update_ibfk_9');
            $table->dropForeign('bot_telegram_update_ibfk_3');
            $table->dropForeign('bot_telegram_update_ibfk_4');
            $table->dropForeign('bot_telegram_update_ibfk_11');
            $table->dropForeign('bot_telegram_update_ibfk_6');
            $table->dropForeign('bot_telegram_update_ibfk_13');
            $table->dropForeign('bot_telegram_update_ibfk_8');
            $table->dropForeign('bot_telegram_update_ibfk_2');
            $table->dropForeign('bot_telegram_update_ibfk_1');
            $table->dropForeign('bot_telegram_update_ibfk_10');
        });

        Schema::table('bot_shipping_query', function (Blueprint $table) {
            $table->dropForeign('bot_shipping_query_ibfk_1');
        });

        Schema::table('bot_pre_checkout_query', function (Blueprint $table) {
            $table->dropForeign('bot_pre_checkout_query_ibfk_1');
        });

        Schema::table('bot_poll_answer', function (Blueprint $table) {
            $table->dropForeign('bot_poll_answer_ibfk_1');
        });

        Schema::table('bot_message', function (Blueprint $table) {
            $table->dropForeign('bot_message_ibfk_5');
            $table->dropForeign('bot_message_ibfk_6');
            $table->dropForeign('bot_message_ibfk_2');
            $table->dropForeign('bot_message_ibfk_4');
            $table->dropForeign('bot_message_ibfk_7');
            $table->dropForeign('bot_message_ibfk_1');
            $table->dropForeign('bot_message_ibfk_3');
        });

        Schema::table('bot_inline_query', function (Blueprint $table) {
            $table->dropForeign('bot_inline_query_ibfk_1');
        });

        Schema::table('bot_edited_message', function (Blueprint $table) {
            $table->dropForeign('bot_edited_message_ibfk_2');
            $table->dropForeign('bot_edited_message_ibfk_3');
            $table->dropForeign('bot_edited_message_ibfk_1');
        });

        Schema::table('bot_conversation', function (Blueprint $table) {
            $table->dropForeign('bot_conversation_ibfk_2');
            $table->dropForeign('bot_conversation_ibfk_1');
        });

        Schema::table('bot_chosen_inline_result', function (Blueprint $table) {
            $table->dropForeign('bot_chosen_inline_result_ibfk_1');
        });

        Schema::table('bot_chat_member_updated', function (Blueprint $table) {
            $table->dropForeign('bot_chat_member_updated_ibfk_2');
            $table->dropForeign('bot_chat_member_updated_ibfk_1');
        });

        Schema::table('bot_chat_join_request', function (Blueprint $table) {
            $table->dropForeign('bot_chat_join_request_ibfk_2');
            $table->dropForeign('bot_chat_join_request_ibfk_1');
        });

        Schema::table('bot_callback_query', function (Blueprint $table) {
            $table->dropForeign('bot_callback_query_ibfk_2');
            $table->dropForeign('bot_callback_query_ibfk_1');
        });

        Schema::dropIfExists('bot_user_chat');

        Schema::dropIfExists('bot_user');

        Schema::dropIfExists('bot_telegram_update');

        Schema::dropIfExists('bot_shipping_query');

        Schema::dropIfExists('bot_request_limiter');

        Schema::dropIfExists('bot_pre_checkout_query');

        Schema::dropIfExists('bot_poll_answer');

        Schema::dropIfExists('bot_poll');

        Schema::dropIfExists('bot_message');

        Schema::dropIfExists('bot_inline_query');

        Schema::dropIfExists('bot_edited_message');

        Schema::dropIfExists('bot_conversation');

        Schema::dropIfExists('bot_chosen_inline_result');

        Schema::dropIfExists('bot_chat_member_updated');

        Schema::dropIfExists('bot_chat_join_request');

        Schema::dropIfExists('bot_chat');

        Schema::dropIfExists('bot_callback_query');
    }
};
