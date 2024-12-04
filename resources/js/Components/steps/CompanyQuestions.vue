<template>
  <div class="space-y-4">
    <h2 class="mb-4 text-xl font-semibold">Jawab Pertanyaan Perusahaan</h2>

    <div v-for="(question, index) in questions" :key="index" class="space-y-2">
      <label :for="`question-${index}`" class="block font-medium">{{
        question.text
      }}</label>
      <textarea
        :id="`question-${index}`"
        v-model="answers[index]"
        rows="3"
        class="w-full p-2 border rounded-md"
        :placeholder="question.placeholder"
      ></textarea>
    </div>
  </div>
</template>

  <script setup>
import { ref, watch } from "vue";

const props = defineProps(["stepData"]);
const emit = defineEmits(["update"]);

const questions = [
  {
    text: "Mengapa Anda tertarik bergabung dengan perusahaan kami?",
    placeholder: "Jelaskan motivasi Anda...",
  },
  {
    text: "Apa pengalaman kerja Anda yang relevan dengan posisi ini?",
    placeholder: "Ceritakan pengalaman Anda...",
  },
  {
    text: "Apa keterampilan utama yang Anda miliki untuk posisi ini?",
    placeholder: "Sebutkan keterampilan Anda...",
  },
];

const answers = ref(props.stepData.answers || questions.map(() => ""));

watch(answers, () => {
  emit("update", { answers: answers.value });
});
</script>
