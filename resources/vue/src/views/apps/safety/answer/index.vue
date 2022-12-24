<template>
  <div>
    <v-dialog v-model="dialogs.answer" width="600" persistent>
      <v-card>
        <v-card-title class="headline lighten-2" primary-title>
          <span class="headline">{{ title }}</span>
        </v-card-title>
        <v-tabs v-model="currentTab" icons-and-text grow>
          <v-tab>
            {{ this.$trans("app.basic_information") }}
            <v-icon>{{ icons.mdiFileDocumentEditOutline }}</v-icon>
          </v-tab>
          <v-tab>
            {{ this.$trans("app.comment_title") }}
            <v-icon>{{ icons.mdiCommentProcessingOutline }}</v-icon>
          </v-tab>
        </v-tabs>
        <v-tabs-items v-model="currentTab" class="mx-8" >
          <v-tab-item :transition="false">
            <v-row class="pt-6">
              <v-col cols="12" class="detail-safety">
                <p>
                  {{ detail }}
                </p>
                <p>
                  <v-icon>
                    {{ icons.mdiLinkVariant }}
                  </v-icon>
                  <a :href="detail_url" :title="detail" target="_blank">{{ detail_url }}</a>
                </p>
              </v-col>
            </v-row>
          </v-tab-item>
          <v-tab-item :transition="false">
            <v-row class="pt-6">
              <v-col cols="12">
                <v-textarea
                  class="textarea-comment"
                  v-model="comment"
                  :label="this.$trans('app.comment_title')"
                  auto-grow
                  outlined
                  rows="10"
                  row-height="15"
                  :rules="[validators.maxLengthValidator(comment, 1023, this.$trans('app.comment_title'))]"
                  :error-messages="errorMessages.comment"
                ></v-textarea>
              </v-col>
            </v-row>
          </v-tab-item>
        </v-tabs-items>
        <v-divider></v-divider>
        <v-card-actions>
          <v-row class="justify-end py-3">
            <v-btn color="default" class="mx-2" @click="close">{{this.$trans("app.close") }}</v-btn>
            <v-btn color="success" class="mx-2" @click="updateAnswer(1)">{{this.$trans("app.safe") }}</v-btn>
            <v-btn color="error" class="mx-2" @click="updateAnswer(2)">{{this.$trans("app.support_required") }}</v-btn>
          </v-row>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import {
  mdiOfficeBuildingOutline,
  mdiMapMarkerOutline,
  mdiFileDocumentEditOutline,
  mdiCommentProcessingOutline,
  mdiLinkVariant
} from "@mdi/js";
import { maxLengthValidator } from "@core/utils/validation";
import store from "@/store";
import StoreModule from "../StoreModule";
import useStore from "../useStore";
import { registerModule } from "@/common/";
import axios from '@axios'
export default {
  props: {
    dialogs: {
      type: Object,
      required: true
    },
    safetyAnswer: {
      type: Object,
      required: true,
      default: {
        detail: "",
        detail_url: "",
        answer: "",
        comment: "",
        answer_urls : {
          answer_urls : '',
          urgent : ''
        }
      }
    }
  },
  watch: {
    safetyAnswer() {
      this.title = this.safetyAnswer.title
      this.detail = this.safetyAnswer.detail;
      this.detail_url = this.safetyAnswer.detail_url;
      this.safety_id = this.safetyAnswer.id;
      this.notified_at = this.safetyAnswer.notify_at;
      this.answer_urls = this.safetyAnswer.answer_urls
    }
  },
  data() {
    return {
      snackbar: {
        isColorSnackbarVisible: false,
        message: "",
        color: ""
      },
      title: '',
      currentTab: null,
      detail: "",
      detail_url: "",
      answer: "",
      comment: "",
      notified_at: "",
      errorMessages: "",
      answer_urls : {
        normal : '',
        urgent : ''
      }
    };
  },
  methods: {
    updateAnswer(urlAnswer) {
      if (this.comment.length > 1023) {
        return;
      }
      axios.post('/safeties/'+this.safety_id+'/answer' , {
        notified_at : this.notified_at,
        answer : urlAnswer,
        comment : this.comment,
      }).then((res) => {
        this.dialogs.answer = false;
        store.commit("setSnackbar",{
          isColorSnackbarVisible: true,
          color : 'success',
          message: trans('message.update_success')
        })
        axios.get('/total-unanswered-notify').then(response => {
            const { count } = response.data
            localStorage.setItem('totalUnansweredNotify', JSON.stringify(count))
            window.location.reload()
        })
      })
    },
    close() {
      this.dialogs.answer = false;
      this.comment = "";
      this.currentTab = null
    },
  },
  setup() {
    registerModule(StoreModule, "safety-replies");
    const { loadingComponent, loading, listSafeties } = useStore();
    return {
      registerModule,
      listSafeties,
      loadingComponent,
      loading,
      validators: {
        maxLengthValidator
      },
      icons: {
        mdiOfficeBuildingOutline,
        mdiMapMarkerOutline,
        mdiFileDocumentEditOutline,
        mdiCommentProcessingOutline,
        mdiLinkVariant
      }
    };
  }
};
</script>
<style scoped>
.v-item-group {
  min-height: 300px !important;
}
.textarea-comment::v-deep textarea{
  max-height: 230px !important;
  overflow: auto;
}
.detail-safety{
  max-height: 275px !important;
  overflow: auto;
}
</style>
