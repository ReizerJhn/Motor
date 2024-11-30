<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HubVenue - Venue Reservation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/lucide-static@0.321.0/font/lucide.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF0000',
                    }
                }
            }
        }
    </script>
</head>

<body class="min-h-screen flex flex-col bg-white">
    <?php include __DIR__ . '/../components/payment.nav.php'; ?>


    <main class="pt-20 flex-grow flex flex-col justify-between p-6 pb-24">
        <div class="max-w-3xl mx-auto w-full">
            <div class="mb-8">
                <h2 class="text-sm font-medium text-gray-500 mb-2">Step <span id="currentStep">1</span> of <span
                        id="totalSteps">5</span></h2>
                <h1 id="stepTitle" class="text-3xl font-bold mb-4">Venue Details</h1>
                <p id="stepDescription" class="text-gray-600 mb-8">Review the details of your selected venue.</p>
            </div>

            <div id="stepContent">
                <!-- Step content will be dynamically inserted here -->
            </div>
        </div>

        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4">
            <div class="flex justify-between items-center max-w-3xl mx-auto">
                <button id="backBtn" class="flex items-center text-sm font-medium text-gray-900" disabled>
                    <i class="lucide-chevron-left h-5 w-5 mr-1"></i>
                    Back
                </button>
                <div class="flex-grow mx-8">
                    <div class="bg-gray-200 h-1 rounded-full">
                        <div id="progressBar" class="bg-black h-1 rounded-full transition-all duration-300 ease-in-out"
                            style="width: 0%"></div>
                    </div>
                </div>
                <button id="nextBtn"
                    class="flex items-center px-6 py-2 bg-black text-white rounded-md text-sm font-medium">
                    Next
                    <i class="lucide-chevron-right h-5 w-5 ml-1"></i>
                </button>
            </div>
        </div>
    </main>

    <script>
        const steps = [
            { title: "Venue Details", description: "Review the details of your selected venue." },
            { title: "Date and Time", description: "Choose your reservation date and time." },
            { title: "Guest Information", description: "Provide details about your event and guests." },
            { title: "Payment", description: "Enter your payment information." },
            { title: "Confirmation", description: "Review and confirm your reservation." }
        ];

        let currentStep = 1;
        const totalSteps = steps.length;
        let formData = {
            date: '',
            time: '',
            durationValue: '',
            durationType: 'days',
            eventType: '',
            guestCount: 1,
            specialRequests: '',
            cardName: '',
            cardNumber: '',
            expirationDate: '',
            cvv: ''
        };

        function updateStep() {
            document.getElementById('currentStep').textContent = currentStep;
            document.getElementById('stepTitle').textContent = steps[currentStep - 1].title;
            document.getElementById('stepDescription').textContent = steps[currentStep - 1].description;
            document.getElementById('progressBar').style.width = `${((currentStep - 1) / (totalSteps - 1)) * 100}%`;

            document.getElementById('backBtn').disabled = currentStep === 1;
            document.getElementById('nextBtn').textContent = currentStep === totalSteps ? 'Confirm Reservation' : 'Next';

            if (currentStep !== totalSteps) {
                document.getElementById('nextBtn').innerHTML += '<i class="lucide-chevron-right h-5 w-5 ml-1"></i>';
            }

            renderStepContent();
        }

        function renderStepContent() {
            const stepContent = document.getElementById('stepContent');
            stepContent.innerHTML = '';

            switch (currentStep) {
                case 1:
                    stepContent.innerHTML = `
                        <div class="space-y-6">
                            <h3 class="text-2xl font-semibold mb-4">Santiago Resort Room 1</h3>
                            <div class="flex items-center text-sm text-gray-500 mb-4">
                                <i class="lucide-map-pin h-4 w-4 mr-1"></i>
                                <span>Cabatangan, Zamboanga City, Zamboanga Peninsula, 7000, Philippines</span>
                            </div>
                            <div class="grid grid-cols-3 gap-6 h-[600px]">
                                <div class="col-span-2 row-span-2">
                                    <img src="/placeholder.svg?height=600&width=800" alt="Santiago Resort Room 1 main image" class="rounded-lg object-cover w-full h-full">
                                </div>
                                <div class="h-[290px]">
                                    <img src="/placeholder.svg?height=300&width=400" alt="Santiago Resort Room 1 image 2" class="rounded-lg object-cover w-full h-full">
                                </div>
                                <div class="relative h-[290px]">
                                    <img src="/placeholder.svg?height=300&width=400" alt="Santiago Resort Room 1 image 3" class="rounded-lg object-cover w-full h-full">
                                    <button class="absolute inset-0 bg-black bg-opacity-50 text-white flex items-center justify-center rounded-lg transition-opacity hover:bg-opacity-75">
                                        Show all photos
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <h4 class="text-lg font-semibold mb-2">Place Description</h4>
                                    <p class="text-gray-600">This Resort offers a wide range of activities suitable for all ages.</p>
                                    <h4 class="text-lg font-semibold mt-4 mb-2">What this place offers</h4>
                                    <ul class="list-disc list-inside text-gray-600">
                                        <li>Pool</li>
                                        <li>Karaoke</li>
                                        <li>Duyan Spot</li>
                                        <li>Shower</li>
                                        <li>Comfort Rooms</li>
                                        <li>Cottages</li>
                                    </ul>
                                </div>
                                <div>
                                    <div class="bg-gray-100 p-4 rounded-lg">
                                        <div class="flex justify-between items-center mb-4">
                                            <span class="text-2xl font-bold">₱500.00</span>
                                            <span class="text-gray-500">per day</span>
                                        </div>
                                        <div class="flex items-center mb-4">
                                            <i class="lucide-star h-5 w-5 text-yellow-400 mr-1"></i>
                                            <span class="text-sm text-gray-600">New listing</span>
                                        </div>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span>Entrance fee</span>
                                                <span>₱100</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Cleaning fee</span>
                                                <span>₱250</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>HubVenue service fee</span>
                                                <span>₱50</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <h4 class="text-lg font-semibold mb-2">Venue Capacity</h4>
                                        <p class="flex items-center text-gray-600">
                                            <i class="lucide-users h-5 w-5 mr-2"></i>
                                            Up to 50 guests
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    break;
                case 2:
                    stepContent.innerHTML = `
                        <div class="space-y-6">
                            <h3 class="text-2xl font-semibold mb-4">Choose Date and Time</h3>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Event Date</label>
                                    <div class="relative">
                                        <input type="date" id="date" name="date" value="${formData.date}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary" required>
                                        <i class="lucide-calendar absolute right-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400"></i>
                                    </div>
                                </div>
                                <div>
                                    <label for="time" class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                                    <div class="relative">
                                        <input type="time" id="time" name="time" value="${formData.time}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary" required>
                                        <i class="lucide-clock absolute right-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="durationValue" class="block text-sm font-medium text-gray-700 mb-1">Event Duration</label>
                                <div class="flex items-center space-x-2">
                                    <input type="number" id="durationValue" name="durationValue" value="${formData.durationValue}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary" required>
                                    <select id="durationType" name="durationType" class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                        <option value="days" ${formData.durationType === 'days' ? 'selected' : ''}>Days</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    `;
                    break;
                case 3:
                    stepContent.innerHTML = `
                        <div class="space-y-6">
                            <h3 class="text-2xl font-semibold mb-4">Guest Information</h3>
                            <div>
                                <label for="eventType" class="block text-sm font-medium text-gray-700 mb-1">Event Type</label>
                                <select id="eventType" name="eventType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary" required>
                                    <option value="" ${formData.eventType === '' ? 'selected' : ''}>Select event type</option>
                                    <option value="wedding" ${formData.eventType === 'wedding' ? 'selected' : ''}>Wedding</option>
                                    <option value="corporate" ${formData.eventType === 'corporate' ? 'selected' : ''}>Corporate Event</option>
                                    <option value="birthday" ${formData.eventType === 'birthday' ? 'selected' : ''}>Birthday Party</option>
                                    <option value="other" ${formData.eventType === 'other' ? 'selected' : ''}>Other</option>
                                </select>
                            </div>
                            <div>
                                <label for="guestCount" class="block text-sm font-medium text-gray-700 mb-1">Number of Guests</label>
                                <div class="flex items-center space-x-2">
                                    <input type="range" id="guestCount" name="guestCount" value="${formData.guestCount}" min="1" max="50" class="w-full">
                                    <span class="text-gray-700">${formData.guestCount}</span>
                                </div>
                            </div>
                            <div>
                                <label for="specialRequests" class="block text-sm font-medium text-gray-700 mb-1">Special Requests</label>
                                <textarea id="specialRequests" name="specialRequests" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">${formData.specialRequests}</textarea>
                            </div>
                        </div>
                    `;
                    break;
                case 4:
                    stepContent.innerHTML = `
                        <div class="space-y-6">
                            <h3 class="text-2xl font-semibold mb-4">Payment Method</h3>
                            <div class="grid grid-cols-2 gap-6">
                                <div class="border rounded-lg p-6 cursor-pointer hover:border-black transition-colors" onclick="selectPaymentMethod('gcash')">
                                    <div class="flex items-center justify-between mb-4">
                                        <img src="/assets/images/gcash-logo.png" alt="GCash" class="h-8">
                                        <input type="radio" name="paymentMethod" value="gcash" class="h-4 w-4">
                                    </div>
                                    <p class="text-sm text-gray-600">Pay securely using your GCash account</p>
                                </div>
                                
                                <div class="border rounded-lg p-6 cursor-pointer hover:border-black transition-colors" onclick="selectPaymentMethod('paymaya')">
                                    <div class="flex items-center justify-between mb-4">
                                        <img src="/assets/images/paymaya-logo.png" alt="PayMaya" class="h-8">
                                        <input type="radio" name="paymentMethod" value="paymaya" class="h-4 w-4">
                                    </div>
                                    <p class="text-sm text-gray-600">Pay using your PayMaya account</p>
                                </div>
                            </div>

                            <div id="qrCodeContainer" class="hidden mt-8">
                                <div class="text-center">
                                    <h4 class="text-lg font-semibold mb-4">Scan QR Code to Pay</h4>
                                    <div class="bg-gray-100 p-8 rounded-lg inline-block">
                                        <img id="qrCodeImage" src="" alt="Payment QR Code" class="mx-auto mb-4">
                                        <p class="text-sm text-gray-600 mb-2">Total Amount: ₱<span id="qrPaymentAmount">0</span></p>
                                        <p class="text-sm text-gray-600">Scan this QR code using your <span id="selectedPaymentMethod"></span> app</p>
                                    </div>
                                    <div class="mt-4">
                                        <p class="text-sm text-gray-600 mb-2">Payment Status: <span id="paymentStatus" class="font-medium">Waiting for payment...</span></p>
                                        <div class="animate-pulse" id="loadingIndicator">
                                            <div class="h-1 w-full bg-gray-200 rounded">
                                                <div class="h-1 bg-blue-500 rounded" style="width: 0%" id="paymentProgress"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    // Add the following JavaScript right after the HTML template
                    const script = document.createElement('script');
                    script.textContent = `
                        async function selectPaymentMethod(method) {
                            // Update radio button
                            document.querySelector(\`input[value="\${method}"]\`).checked = true;
                            
                            // Show QR code container
                            const qrContainer = document.getElementById('qrCodeContainer');
                            qrContainer.classList.remove('hidden');
                            
                            // Update payment method text
                            document.getElementById('selectedPaymentMethod').textContent = 
                                method === 'gcash' ? 'GCash' : 'PayMaya';
                            
                            // Calculate total amount
                            const basePrice = 500 * parseInt(formData.durationValue);
                            const totalAmount = basePrice + 100 + 250 + 50;
                            document.getElementById('qrPaymentAmount').textContent = totalAmount;
                            
                            try {
                                // Initiate payment
                                const response = await fetch('../payment/process-payment.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        paymentMethod: method,
                                        amount: totalAmount,
                                        reservationDetails: formData
                                    })
                                });
                                
                                const data = await response.json();
                                
                                if (data.success) {
                                    // Display QR code
                                    document.getElementById('qrCodeImage').src = data.qrCode;
                                    // Store reference number
                                    formData.paymentReference = data.reference;
                                    // Start checking payment status
                                    checkPaymentStatus(data.reference);
                                } else {
                                    throw new Error(data.message);
                                }
                            } catch (error) {
                                alert('Error initiating payment: ' + error.message);
                            }
                        }

                        async function checkPaymentStatus(reference) {
                            const progressBar = document.getElementById('paymentProgress');
                            const paymentStatus = document.getElementById('paymentStatus');
                            const loadingIndicator = document.getElementById('loadingIndicator');
                            const nextBtn = document.getElementById('nextBtn');
                            
                            try {
                                const response = await fetch(\`/payment/process-payment.php?reference=\${reference}\`);
                                const data = await response.json();
                                
                                if (data.status === 'completed') {
                                    progressBar.style.width = '100%';
                                    paymentStatus.textContent = 'Payment completed!';
                                    paymentStatus.classList.add('text-green-600');
                                    loadingIndicator.classList.remove('animate-pulse');
                                    nextBtn.disabled = false;
                                    return;
                                }
                                
                                // Continue checking status every 3 seconds
                                setTimeout(() => checkPaymentStatus(reference), 3000);
                                
                            } catch (error) {
                                paymentStatus.textContent = 'Error checking payment status';
                                paymentStatus.classList.add('text-red-600');
                            }
                        }
                    `;
                    document.body.appendChild(script);
                    break;
                case 5:
                    stepContent.innerHTML = `
                        <div class="space-y-6">
                            <h3 class="text-2xl font-semibold mb-4">Reservation Summary</h3>
                            <div class="bg-gray-100 p-6 rounded-lg">
                                <h4 class="font-semibold text-lg mb-4">Santiago Resort Room 1</h4>
                                <div class="space-y-2">
                                    <p><strong>Date:</strong> ${formData.date}</p>
                                    <p><strong>Time:</strong> ${formData.time}</p>
                                    <p><strong>Duration:</strong> ${formData.durationValue} ${formData.durationType}</p>
                                    <p><strong>Event Type:</strong> ${formData.eventType}</p>
                                    <p><strong>Guests:</strong> ${formData.guestCount}</p>
                                    <p><strong>Special Requests:</strong> ${formData.specialRequests || 'None'}</p>
                                </div>
                                <div class="mt-6 pt-4 border-t border-gray-300">
                                    <h5 class="font-semibold mb-2">Price Breakdown</h5>
                                    <div class="space-y-1">
                                        <div class="flex justify-between">
                                            <span>Base Price (₱500 x ${formData.durationValue} ${formData.durationType})</span>
                                            <span>₱${500 * parseInt(formData.durationValue)}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Entrance Fee</span>
                                            <span>₱100</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Cleaning Fee</span>
                                            <span>₱250</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>HubVenue Service Fee</span>
                                            <span>₱50</span>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-gray-300 flex justify-between font-semibold">
                                        <span>Total</span>
                                        <span>₱${500 * parseInt(formData.durationValue) + 100 + 250 + 50}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="lucide-check h-5 w-5 text-green-500 mr-2"></i>
                                <p class="text-sm text-gray-600">By clicking "Confirm Reservation", you agree to our terms and conditions.</p>
                            </div>
                        </div>
                    `;
                    break;
            }

            // Add event listeners to form inputs
            const inputs = stepContent.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('change', (e) => {
                    formData[e.target.name] = e.target.value;
                });
            });
        }

        document.getElementById('backBtn').addEventListener('click', () => {
            if (currentStep > 1) {
                currentStep--;
                updateStep();
            }
        });

        document.getElementById('nextBtn').addEventListener('click', () => {
            if (currentStep < totalSteps) {
                currentStep++;
                updateStep();
            } else {
                // Handle form submission
                console.log('Form submitted:', formData);
                alert('Reservation confirmed!');
            }
        });

        // Initialize the form
        updateStep();
    </script>
</body>

</html>