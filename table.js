let currentMonday = getStartOfWeek();

// returns DateTime object for the current week's monday
function getStartOfWeek()
{
    const today = new Date();
    const dayOfWeek = today.getDay();
    const daysSinceMonday = (dayOfWeek + 6) % 7;
    const monday = new Date(today);
    monday.setDate(today.getDate() - daysSinceMonday);
    monday.setHours(0, 0, 0, 0);
    return monday;
}

// takes DateTime object and returns it as a string formatted as "YYYY-MM-DD"
function formatDate(date)
{
    let month = (date.getMonth() + 1).toString().padStart(2, "0");
    let day = date.getDate().toString().padStart(2, "0");
    return `${date.getFullYear()}-${month}-${day}`;
}

function formatDateUK(date)
{
    let month = (date.getMonth() + 1).toString().padStart(2, "0");
    let day = date.getDate().toString().padStart(2, "0");
    return `${day}/${month}/${date.getFullYear()}`;
}

document.getElementById("leftWeekButton").addEventListener("click", () => {
    currentMonday.setDate(currentMonday.getDate() - 7);
    drawTable(currentMonday);
});

document.getElementById("rightWeekButton").addEventListener("click", () => {
    currentMonday.setDate(currentMonday.getDate() + 7);
    drawTable(currentMonday);
});

async function drawTable(startDate)
{
    document.getElementById("weekTitle").innerText = "Week starting " + formatDateUK(startDate);

    const tbody = document.querySelector("#weekTable tbody");
    tbody.innerHTML = "";

    for (var i = 0; i < 7; i++)
    {
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + i);

        const row = document.createElement("tr");

        const dateCell = document.createElement("td");
        dateCell.innerText = date.toLocaleDateString();
        row.appendChild(dateCell);

        const calorieCell = document.createElement("td");
        const calorieInput = document.createElement("input");
        calorieInput.type = "number";
        
        let calorieData = await getCalorieData(date);

        calorieInput.value = calorieData;
        calorieInput.disabled = true;
        calorieInput.id = `caloriesInput-${formatDate(date)}`;
        calorieCell.appendChild(calorieInput);
        row.appendChild(calorieCell);

        const actionCell = document.createElement("td");
        const actionButton = document.createElement("button");
        actionButton.innerText = "Edit";
        actionButton.addEventListener("click", () => toggleEdit(actionButton, calorieInput, date));
        actionCell.appendChild(actionButton);
        row.appendChild(actionCell);
        tbody.appendChild(row);
    }
}

async function toggleEdit(button, input, date)
{
    if (input.disabled)
    {
        input.disabled = false;
        button.innerText = "Submit";
    }
    else
    {
        let success = await updateCalorieData(date, input.value);
        if (success)
        {
            input.disabled = true;
            button.innerText = "Edit";
        }
    }
}

async function updateCalorieData(date, val)
{
    let promise = new Promise(function(resolve, reject)
    {
        $.ajax({
            url: "data_handler.php",
            method: "POST",
            data:
            {
                "date": formatDate(date),
                "calories": val,
                "token": document.querySelector("meta[name='csrf-token']").getAttribute("content")
            },
            dataType: "json",
            success: function (response)
            {
                if (response["success"] == true)
                {
                    resolve(true);
                }
                else
                {
                    console.log(response);
                    resolve(false);
                }
            },
            error: function (xhr, status, error) {
                reject(error);
            }

        });
    });
    try
    {
        let success = await promise;
        return success;
    }
    catch
    {
        console.log("Error posting data: ", error);
        return false;
    }
}

async function getCalorieData(date)
{
    let promise = new Promise(function(resolve, reject) 
    {
        $.ajax({
            url: "data_handler.php",
            method: "GET",
            data: 
            {
                "date": formatDate(date), 
                "token": document.querySelector("meta[name='csrf-token']").getAttribute("content")
            },
            dataType: "json",
            success: function (response) {
                if (response["success"] == true)
                {
                    resolve(response["calories"]);
                }
                else
                {
                    reject(new Error(response["error"]));
                }
            },
            error: function (xhr, status, error) {
                reject(error);
            }
        });
    });
    try
    {
        let data = await promise;
        return data;
    }
    catch (error)
    {
        console.log("Error fetching data: ", error);
        return "";
    }
}

drawTable(currentMonday);