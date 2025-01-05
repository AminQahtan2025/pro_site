const categories = [
    { name: "القدرات العامة", questions: 5, threshold: 15 },
    { name: "قدرات القيادة الذاتية", questions: 5, threshold: 15 },
    { name: "قيادة فرق العمل", questions: 6, threshold: 18 },
    { name: "القيادة التنظيمية", questions: 6, threshold: 18 },
    { name: "القيادة التكنولوجية", questions: 4, threshold: 12 },
    { name: "القيادة الإبداعية والريادية", questions: 5, threshold: 15 },
    { name: "القيادة الفنية", questions: 4, threshold: 12 }
];

const questionnaire = [
// Category 1: القدرات العامة
"لدي المعرفة والمهارة التي تمكنني من امتلاك القدرة على التأثير الإيجابي على الآخرين وإقناعهم بالأهداف التي أسعى الى تحقيقها.",
"أمتلك المعرفة والقدرة على التفكير الاستراتيجي الهادف الى تطوير خطط استراتيجية بعيدة المدى.",
"أمتلك المعرفة والقدرة التي تمكنني من إدارة وقتي وترتيب أولوياتي بكفاءة وفعالية.",
"لدي عقلية متفتحة على الأفكار الجديدة وأتقبل النقد البناء وأشجع الأفكار الجديدة في العمل والتي قد تخالف أفكاري وطريقة عملي.",
"أمتلك المعرفة والقدرة على وضع الأهداف الذكية الصعبة والتي تثير التحدي وتضمن تكاتف كل الجهود من أجل تحقيقها.",

// Category 2: قدرات القيادة الذاتية
"أمتلك القدرة على فهم عواطفي وانفعالاتي والتحكم فيهما.",
"أمتلك المعرفة والقدرات التي تمكنني من تحمل مسؤولياتي ولدي الشجاعة في الاعتراف بالأخطاء إن حدثت.",
"أمتلك المعرفة والمهارة التي تمكنني من القدرة على التكيف بشكل جيد مع التغيير ولدي المرونة للتعامل مع الظروف الطارئة بكل ثقة واقتدار.",
"دائماً لدي الشغف في التعلم المستمر من أجل تطوير قدراتي الفنية والقيادية من خلال التدريب المباشر أو الالكتروني والحصول على شهادات مهنية من أرقى المنظمات العالمية.",
"أمتلك القدرة على الصبر والتحمل في مواجهة الظروف الصعبة ولدي القدرة على استعادة التوازن الذاتي.",

// Category 3: قيادة فرق العمل
"أمتلك المعرفة والقدرة على فهم الآخرين وعواطفهم وانفعالاتهم ولدي القدرة على قيادتها والتحكم فيها.",
"أمتلك القدرة على تقديم نموذج قيادي ممتاز لتوجيه الآخرين والتأثير فيهم بناءً على مبدأ القيادة بالقدوة.",
"لدي الخبرة والمهارة التي تمكنني من التعامل مع النزاعات والصراعات التي قد تحدث بطريقة بناءة، بما يضمن إيجاد حلول تعود بالفائدة على جميع الأطراف.",
"أمتلك الخبرة والمهارة التي تمكنني من القدرة على التواصل الفعال مع الآخرين والتعبير الواضح عن أفكاري ومشاعري.",
"أمتلك القدرة على تقدير جهود الآخرين ولا أعمل على التقليل منها وأعمل على الاعتراف بإنجازاتهم بكل إنصاف وأعمل على مكافأتها.",
"أمتلك المعرفة والقدرة على تدريب الآخرين وإرشادهم والإشراف عليهم بعد تحديد نقاط ضعفهم وتحديد حاجاتهم التدريبية.",

// Category 4: القيادة التنظيمية
"أمتلك المعرفة العلمية الضرورية والقدرات والمهارات التي تمكنني من تخطيط التغيير وإدارته بكفاءة وفعالية.",
"أمتلك المعرفة العلمية والقدرات التي تساعدني على تطبيق التفكير المنطقي التحليلي من أجل حل المشكلات.",
"أمتلك المعرفة العلمية والقدرات والمهارات التي تمكنني من اتخاذ القرار العقلاني المبني على الدراسة والتحليل مع القدرة على الاستفادة من الخبرة والبصيرة.",
"أمتلك الخبرة والكفاءة التي تمكنني من القيام بأبحاث علمية وفق الخطوات العلمية في البحث العلمي من أجل حل المشكلات وتقديم حلول.",
"أمتلك المعرفة والقدرات التي تمكنني من بناء فريق عمل فعال وإدارته بكفاءة وفعالية بما يساهم في التغلب على العقبات من أجل تحقيق الأهداف التنظيمية.",
"أمتلك الخبرة والقدرة على بناء شبكة علاقات فعالة والاستفادة من هذه العلاقات في تحقيق الأهداف المهنية والتنظيمية.",

// Category 5: القيادة التكنولوجية
"لدي شغف كبير بكل جديد في عالم التكنولوجيا وكيفية الاستفادة منه على المستوى الشخصي والعملي.",
"لدي الخبرة والكفاءة للتعامل مع الاتجاهات والتحولات الجديدة في عالم التكنولوجيا مثل الذكاء الاصطناعي وأنظمة الأتمتة والبيانات الضخمة وإنترنت الأشياء.",
"أمتلك المعرفة العلمية والقدرات التي تمكنني من تحليل الأعمال واتخاذ قرارات موجهة بالبيانات والحقائق.",
"أمتلك الثقافة الرقمية ولدي انفتاح لتقبل الجديد في عالم التكنولوجيا وتجربته وقياس فوائده على المستوى الشخصي والتنظيمي.",

// Category 6: القيادة الإبداعية والريادية
"أمتلك الرغبة الدائمة في الابتكار والتطوير ولدي المعرفة العلمية بخطوات التفكير الابتكاري الإبداعي.",
"دائماً أمتلك الرغبة في المخاطرة المدروسة والرغبة الدائمة في تحدي الغموض أو المجهول بعد إجراء الدراسات اللازمة.",
"أمتلك المعرفة والقدرة على تشجيع الآخرين على الإبداع والابتكار ودائماً أسعى الى خلق بيئة إيجابية تشجع على ذلك.",
"لدي المعرفة والقدرة على التفكير التصميمي الذي يبدأ بتحديد المشكلة والتدرج في خطوات علمية منطقية للوصول الى التصميم المناسب للحل.",
"لدي الشغف والمعرفة والقدرة على البحث عن الفرص التي تمكنني من تطوير الأعمال وتحويل الأفكار الى مشاريع ناجحة.",

// Category 7: القيادة الفنية
"أمتلك المعرفة العلمية والكفاءة المهنية والقدرات الوظيفية في مجال تخصصي.",
"أمتلك الشغف والرغبة الدائمة على الاطلاع على كل جديد في مجال تخصصي وأشعر بالمتعة في ذلك.",
"لدي الرغبة الدائمة في التميز المعرفي وفي مجال القدرات والمهارات في مجال تخصصي وأحب دائماً أن أكون المرجع في ذلك.",
"لدي المعرفة والقدرة على التعامل الفني مع الآخرين وتقديم الدعم والمشورة والتوجيه اللازم للعاملين في مجال تخصصي."
];
const questionsContainer = document.getElementById("questionsContainer");
const progressBar = document.getElementById("progressBar");
const introMessage = document.getElementById("introMessage");
const continueButton = document.getElementById("continueButton");
const resultSection = document.getElementById("resultSection");
const resultText = document.getElementById("resultText");

let currentCategoryIndex = 0;
let scores = new Array(categories.length).fill(0);

function renderCategory(index) {
    questionsContainer.innerHTML = "";
    const category = categories[index];
    introMessage.innerHTML = `القسم (الفئة) ${index + 1}: ${category.name}`;

    const categorySection = document.createElement("div");
    categorySection.classList.add("category-section");
    categorySection.style.display = "block";

    for (let i = 0; i < category.questions; i++) {
        const questionId = `question${index}_${i}`;
        const questionHTML = `
            <div class="mb-3">
                <label class="form-label">${questionnaire.shift()}</label>
                <select class="form-select" name="${questionId}" required>
                    <option value="" selected>اختر الإجابة...</option>
                    <option value="5">إلى حد كبير</option>
                    <option value="4">إلى حد ما</option>
                    <option value="3">متعادل</option>
                    <option value="2">إلى حد ضعيف</option>
                    <option value="1">إلى حد ضعيف جدًا</option>
                </select>
                <div class="invalid-feedback">يرجى تحديد الإجابة.</div>
            </div>`;
        categorySection.insertAdjacentHTML("beforeend", questionHTML);
    }

    questionsContainer.appendChild(categorySection);
}

function calculateCategoryScore() {
    const categoryFormData = new FormData(document.getElementById("questionnaireForm"));
    let totalScore = 0;

    for (const value of categoryFormData.values()) {
        totalScore += parseInt(value);
    }

    scores[currentCategoryIndex] = totalScore;
}

continueButton.addEventListener("click", () => {
    const categoryInputs = document.querySelectorAll(`#questionsContainer select`);
    let allAnswered = true;

    // Validate all dropdowns
    categoryInputs.forEach((input) => {
        if (!input.value) {
            input.classList.add("is-invalid");
            allAnswered = false;
        } else {
            input.classList.remove("is-invalid");
        }
    });

    // If any dropdown is unanswered, show a message and stop the process
    if (!allAnswered) {
        alert("يرجى الإجابة على جميع الأسئلة قبل المتابعة إلى الفئة التالية.");
        return;
    }

    // Calculate score for the current category
    calculateCategoryScore();

    // Move to the next category or show results if it's the last category
    currentCategoryIndex++;

    if (currentCategoryIndex < categories.length) {
        renderCategory(currentCategoryIndex);
        updateProgressBar();
    } else {
        showResults();
    }
});


function updateProgressBar() {
    const progress = ((currentCategoryIndex) / categories.length) * 100;
    progressBar.style.width = `${progress}%`;
    progressBar.setAttribute("aria-valuenow", progress);
}

function showResults() {
    continueButton.style.display = "none";
    resultSection.classList.remove("d-none");
    introMessage.innerHTML = "تحليل النتائج";

    let result = `
    <h4 class="mt-4">الجاهزية القيادية</h4>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>الفئة</th>
                    <th>النقاط المكتسبة</th>
                    <th>النقاط القصوى</th>
                    <th>التقييم</th>
                </tr>
            </thead>
            <tbody>
    `;

    categories.forEach((category, index) => {
        const maxPoints = category.questions * 5;
        const evaluation =
            scores[index] <= category.threshold
                ? `<p class="text-danger">تحتاج أن تركز على تطوير قدرات ${category.name}</p>`
                : "<p class='text-success'>جاهز لهذه الكفاءة </p> ";
        result += `
            <tr>
                <td>${category.name}</td>
                <td>${scores[index]}</td>
                <td>${maxPoints}</td>
                <td>${evaluation}</td>
            </tr>
        `;
    });

    result += `
            </tbody>
        </table>
    `;

    questionsContainer.innerHTML = result;
}
        renderCategory(currentCategoryIndex);
updateProgressBar();