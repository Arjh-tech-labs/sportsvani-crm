import { NextResponse } from "next/server"

// In a real application, you would use a database and proper authentication
// This is a simplified example for demonstration purposes

export async function POST(request: Request) {
  try {
    const { email, password } = await request.json()

    // Check credentials (in a real app, you would verify against a database)
    if (email === "sportavani@gmail.com" && password === "12345678") {
      // Generate a token (in a real app, use JWT or similar)
      const token = "demo_token_" + Math.random().toString(36).substring(2, 15)

      return NextResponse.json({
        success: true,
        token,
        user: {
          id: 1,
          email: "sportavani@gmail.com",
          role: "superadmin",
          name: "Super Admin",
        },
      })
    }

    // Invalid credentials
    return NextResponse.json({ success: false, message: "Invalid email or password" }, { status: 401 })
  } catch (error) {
    console.error("Login error:", error)
    return NextResponse.json({ success: false, message: "An error occurred during login" }, { status: 500 })
  }
}

