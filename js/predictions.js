document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("[data-prediction-form]");
  if (!form) return;

  form.addEventListener("submit", async (event) => {
    event.preventDefault();

    const submitButton = form.querySelector("[type=submit]");
    const status = form.querySelector("[data-prediction-status]");
    const scoresByMatch = new Map();

    for (const input of form.querySelectorAll("[data-prediction-match]")) {
      const matchId = input.dataset.predictionMatch;
      const side = input.dataset.predictionSide;
      const scores = scoresByMatch.get(matchId) || { match_id: Number(matchId) };
      scores[side] = input.value;
      scoresByMatch.set(matchId, scores);
    }

    const predictions = Array.from(scoresByMatch.values()).filter(
      (prediction) => prediction.home_score !== "" && prediction.away_score !== ""
    );

    submitButton.disabled = true;
    if (status) status.textContent = "Saving...";

    try {
      const response = await fetch(form.action, {
        method: "POST",
        credentials: "same-origin",
        headers: {
          "Accept": "application/json",
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          csrf_token: form.elements.csrf_token.value,
          predictions
        })
      });
      const result = await response.json();
      if (!response.ok || !result.success) {
        throw new Error(result.error || "Unable to save predictions.");
      }
      if (status) status.textContent = "Saved.";
      if (result.redirect) window.location.assign(result.redirect);
    } catch (error) {
      if (status) status.textContent = error.message;
      submitButton.disabled = false;
    }
  });
});