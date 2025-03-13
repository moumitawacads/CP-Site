<template>
    <div>
        <div class="accordion p-12 mb-2" id="accordionFaq" v-for="(gameSession, gameSessionIndex) in paginatedSessions"
            :key="gameSession.id">
            <div class="accordion-item">
                <h2 class="accordion-header" :id="'faqDetails' + gameSession.id">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        :data-bs-target="'#collapseTwo' + gameSession.id" aria-expanded="false"
                        :aria-controls="'collapseTwo' + gameSession.id">
                        <b>Session {{ calculateSessionNumber(gameSessionIndex) }}</b>
                    </button>
                </h2>
                <div :id="'collapseTwo' + gameSession.id" class="accordion-collapse collapse"
                    :aria-labelledby="'faqDetails' + gameSession.id" data-bs-parent="#accordionFaq">
                    <div class="accordion-body">
                        <p>
                            <b>Session Start:</b>
                            {{ formatDate(gameSession.session_start_time) }}
                        </p>
                        <p><b>Session End:</b> {{ formatDate(gameSession.session_end_time) }}</p>
                        <label><b>Games Played:</b></label>
                        <table class="table table-striped table-bordered table-responsive mt-2">
                            <thead>
                                <tr>
                                    <th>Position</th>
                                    <th>Coins Earned</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ gameSession.pick_possition }}</td>
                                    <td>{{ gameSession.coins_earned }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="table table-striped table-bordered table-responsive mt-3">
                            <thead>
                                <tr>
                                    <th>Game Name</th>
                                    <th>No. of Correct</th>
                                    <th>No. of Incorrect</th>
                                    <th>No. of Attempts</th>
                                    <th>Hit</th>
                                    <th>Miss</th>
                                    <th>Game Score</th>
                                    <th>Audio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="game in gameSession.session_games" :key="game.id">
                                    <td>{{ game.game_name }}</td>
                                    <td>{{ numberOfCorrect(game.learner_game_levels) }}</td>
                                    <td>{{ numberOfIncorrect(game.learner_game_levels) }}</td>
                                    <td>{{ game.number_of_attempts }}</td>
                                    <td>{{ game.number_of_attempts - (game.number_of_attempts - game.game_score) }}</td>
                                    <td>{{ game.number_of_attempts - game.game_score }}</td>
                                    <td>{{ game.game_score }}</td>
                                    <td>
                                        <table>
                                            <tbody>
                                                <tr v-for="learner_game_level in game.learner_game_levels"
                                                    :key="learner_game_level.id">
                                                    <td>
                                                        <template v-if="learner_game_level.audio_file_path">
                                                            <button v-if="isPlaying(learner_game_level.audio_file_path)"
                                                                class="btn btn-primary btn-sm"
                                                                @click.prevent="toggleSound(learner_game_level.audio_file_path)">
                                                                <span class="fa fa-pause-circle"></span>
                                                            </button>
                                                            <button v-else class="btn btn-primary btn-sm"
                                                                @click.prevent="toggleSound(learner_game_level.audio_file_path)">
                                                                <span class="fa fa-play-circle"></span>
                                                            </button>
                                                        </template>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pagination Controls -->
        <nav aria-label="Page navigation example" class="mt-4" v-if="this.sortedSessions.length > itemsPerPage">
            <ul class="pagination justify-content-center">
                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                    <a class="page-link" href="#" @click.prevent="prevPage">Previous</a>
                </li>
                <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: currentPage === page }">
                    <a class="page-link" href="#" @click.prevent="goToPage(page)">{{ page }}</a>
                </li>
                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                    <a class="page-link" href="#" @click.prevent="nextPage">Next</a>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script>
import moment from "moment";

export default {
    name: "Sessions",
    props: ["sortedSessions"],
    data() {
        return {
            currentAudio: null,
            currentAudioPath: null,
            currentPage: 1,
            itemsPerPage: 10,
        }
    },
    computed: {
        paginatedSessions() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.sortedSessions.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.sortedSessions.length / this.itemsPerPage);
        },
    },
    methods: {
        getFullStoragePath(sound) {
            // console.log(import.meta.env.MODE, `${window.location.origin}${import.meta.env.MODE === 'development' ? '' : '/public'}/storage/${sound}`);
            return `${window.location.origin}${import.meta.env.MODE === 'development' ? '' : '/public'}/storage/${sound}`;
        },
        playSound(sound) {
            if (this.currentAudio) {
                this.currentAudio.pause();
                this.currentAudio.currentTime = 0;
            }
            const fullPath = this.getFullStoragePath(sound);
            this.currentAudio = new Audio(fullPath);
            this.currentAudio.play();
            this.currentAudioPath = sound;

            // Listen for the ended event to reset the button class
            this.currentAudio.addEventListener('ended', () => {
                this.currentAudio = null;
                this.currentAudioPath = null;
            });
        },
        pauseSound() {
            if (this.currentAudio) {
                this.currentAudio.pause();
                this.currentAudio = null; this.currentAudioPath = null;
            }
        },
        toggleSound(sound) {
            if (this.currentAudioPath === sound) {
                this.pauseSound();
            } else {
                this.playSound(sound);
            }
        },
        isPlaying(sound) {
            return this.currentAudioPath === sound;
        },
        numberOfCorrect(sessionGames) {
            return sessionGames.filter(game => {
                return game.answer_type === 'Correct'
            }).length
        },
        numberOfIncorrect(sessionGames) {
            return sessionGames.filter(game => {
                return game.answer_type === 'Incorrect'
            }).length
        },
        formatDate(value) {
            if (value) {
                // let stillUtc = moment.utc(value).toDate();
                return moment(value).local().format("MMM DD, YYYY - hh:mm A");
            }
        },
        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },
        goToPage(page) {
            this.currentPage = page;
        },
        calculateSessionNumber(gameSessionIndex) {
            return this.sortedSessions.length - ((this.currentPage - 1) * this.itemsPerPage + gameSessionIndex);
        },
    }
}; 
</script>

<style scoped>
.fa-circle-play {
    font-size: 1.32rem;
}

.fa-circle-pause {
    font-size: 1.32rem;
}
</style>
