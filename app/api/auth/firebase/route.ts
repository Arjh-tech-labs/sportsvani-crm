import { NextResponse } from "next/server"
import { firebaseAuth } from "@/lib/firebase"

// Send OTP to phone number
export async function POST(request: Request) {
  try {
    const { phoneNumber } = await request.json()

    if (!phoneNumber) {
      return NextResponse.json({ success: false, message: "Phone number is required" }, { status: 400 })
    }

    // Send OTP via Firebase Auth
    const result = await firebaseAuth.sendOTP(phoneNumber)

    return NextResponse.json({
      success: true,
      message: "OTP sent successfully",
      verificationId: result.verificationId,
    })
  } catch (error) {
    console.error("Error sending OTP:", error)
    return NextResponse.json({ success: false, message: "An error occurred while sending OTP" }, { status: 500 })
  }
}

// Verify OTP
export async function PUT(request: Request) {
  try {
    const { verificationId, otp } = await request.json()

    if (!verificationId || !otp) {
      return NextResponse.json({ success: false, message: "Verification ID and OTP are required" }, { status: 400 })
    }

    // Verify OTP via Firebase Auth
    const result = await firebaseAuth.verifyOTP(verificationId, otp)

    if (result.success) {
      // Generate a token (in a real app, use JWT or similar)
      const token = "firebase_token_" + Math.random().toString(36).substring(2, 15)

      return NextResponse.json({
        success: true,
        message: "OTP verified successfully",
        token,
        user: result.user,
      })
    } else {
      return NextResponse.json({ success: false, message: "Invalid OTP" }, { status: 400 })
    }
  } catch (error) {
    console.error("Error verifying OTP:", error)
    return NextResponse.json({ success: false, message: "An error occurred while verifying OTP" }, { status: 500 })
  }
}

